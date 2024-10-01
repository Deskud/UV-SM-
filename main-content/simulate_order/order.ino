#include <Ethernet.h>
#include <SPI.h>
#include <ArduinoJson.h>
#include <Keypad.h>

// Network settings
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
IPAddress ip(192, 168, 1, 100);     // Arduino IP
IPAddress server(192, 168, 1, 3);  // Server IP
int port = 80;                      // HTTP port

EthernetClient client;

// Define the number of rows and columns
const byte ROWS = 4;
const byte COLS = 4;

// Define the symbols on the keypad buttons
char keys[ROWS][COLS] = {
  {'1', '4', '7', '*'},
  {'2', '5', '8', '0'},
  {'3', '6', '9', '#'},
  {'A', 'B', 'C', 'D'}
};

// Connect keypad ROW0, ROW1, ROW2, and ROW3 to these Arduino pins
byte rowPins[ROWS] = {21, 20, 19, 18};

// Connect keypad COL0, COL1, COL2, and COL3 to these Arduino pins
byte colPins[COLS] = {17, 16, 15, 14};

// Create the Keypad object
Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);

// To hold product data
struct Product {
  String name;
  int product_id;
  char size;
  int quantity;
};

// New structure for order details
struct Order {
  int product_id;
  char size;
  int quantity;
};

Product products[24];  // Product list
Order orders[6];       // Array to hold up to 6 orders
int productCount = 0;
int orderCount = 0;
int studentId = 0;

void setup() {
  Serial.begin(9600);

  // Initialize Ethernet
  Ethernet.begin(mac, ip);
  delay(1000);
}

void loop() {
  // Wait for input to start fetching products
  Serial.println("Press '#' to start fetching products...");
  waitForKey('#');

  // Fetch products from the server
  fetchProducts();

  // Ask the user to select a product
  if (productCount > 0) {
    studentId = waitForStudentIdInput();  // Get student ID
    Serial.print("Student ID entered: ");
    Serial.println(studentId);  // Print entered ID for confirmation
    orderCount = 0;             // Reset order count for new order

    while (orderCount < 6) {
      Serial.println("Please select a product by entering the corresponding number (1-9).");
      int selectedProductIndex = waitForProductSelection();

      // Ask for size selection
      char size = waitForSizeSelection();

      // Ask for the quantity (limit to 1 or 2)
      int quantity = waitForQuantityInput();

      // Store order details separately
      orders[orderCount].product_id = products[selectedProductIndex].product_id;  // Store product_id
      orders[orderCount].size = size;
      orders[orderCount].quantity = quantity;
      orderCount++;

      // Debugging output for order details
      Serial.print("Order Details for Product ");
      Serial.println(orderCount);
      Serial.print("Product ID: ");
      Serial.println(orders[orderCount - 1].product_id);  // Access the last order
      Serial.print("Size: ");
      Serial.println(orders[orderCount - 1].size);
      Serial.print("Quantity: ");
      Serial.println(orders[orderCount - 1].quantity);

      // Ask if the user wants to add another product
      Serial.println("Do you want to add another product? (1=Yes/3=No): ");
      char key = keypad.getKey();
      while (key != '1' && key != '3') {
        key = keypad.getKey();
        delay(100);
      }
      if (key == '3') break;  // Exit loop if user does not want to add more
    }

    // Send order details to the server
    sendOrderDetails(studentId, orderCount);
  }
}

void fetchProducts() {
  if (client.connect(server, port)) {
    Serial.println("Connected to server");

    // Send HTTP GET request to the server
    client.println("GET /vm-unif/main-content/get_products.php HTTP/1.1");
    client.println("Host: 192.168.1.22");
    client.println("User-Agent: Arduino/1.0");
    client.println("Connection: close");
    client.println();
  } else {
    Serial.println("Connection failed");
    return;
  }

  // Wait for server response
  while (client.connected() && !client.available()) {
    delay(10);
  }

  // Read the server response
  String jsonResponse = "";
  bool isBody = false;

  // Read line by line and filter headers
  while (client.available()) {
    String line = client.readStringUntil('\n');

    // Detect end of headers
    if (line == "\r") {
      isBody = true;
      continue;
    }

    // Collect the JSON response
    if (isBody) {
      jsonResponse += line;
    }
  }

  // Close the connection
  client.stop();
  Serial.println("Disconnected from server");

  // Print the raw JSON response for debugging
  Serial.println("Raw JSON Response:");
  Serial.println(jsonResponse);

  // Parse JSON data
  parseJsonData(jsonResponse);
}

void parseJsonData(String jsonResponse) {
  if (jsonResponse.length() == 0) {
    Serial.println("JSON response is empty.");
    return;
  }

  // Estimate size based on expected data
  const size_t capacity = JSON_ARRAY_SIZE(24) + 24 * JSON_OBJECT_SIZE(3) + 24 * 50;  // Updated for product_id
  DynamicJsonDocument doc(capacity);

  // Parse the JSON response
  DeserializationError error = deserializeJson(doc, jsonResponse);

  if (error) {
    Serial.print("Failed to parse JSON: ");
    Serial.println(error.c_str());
    return;
  }

  // Iterate over each product in the JSON array
  JsonArray productsArray = doc.as<JsonArray>();
  productCount = 0;

  Serial.println("Available Products:");
  int index = 1;
  for (JsonObject product : productsArray) {
    if (product.containsKey("product_name") && product.containsKey("product_quantity") && product.containsKey("product_id")) {
      const char* product_name = product["product_name"];
      const int product_id = product["product_id"];  // Read product_id
      int quantity = product["product_quantity"];

      // Store the product in the array
      products[productCount].name = product_name;
      products[productCount].product_id = product_id;  // Store product_id
      products[productCount].quantity = quantity;
      productCount++;

      // Print product with index for selection
      Serial.print(index);
      Serial.print(". Product: ");
      Serial.print(product_name);
      Serial.print(", ID: ");
      Serial.print(product_id);  // Print product ID
      Serial.print(", Quantity: ");
      Serial.println(quantity);
      index++;
    } else {
      Serial.println("Unexpected JSON format.");
    }
  }
}

void waitForKey(char expectedKey) {
  char key = 0;
  while (key != expectedKey) {
    key = keypad.getKey();
    delay(100);  // Small delay to prevent excessive CPU usage
  }
}

int waitForStudentIdInput() {
  char key = 0;
  String idString = "";  // To build the student ID as a string

  Serial.println("Enter Student ID (up to 3 digits):");

  while (idString.length() < 3) {
    key = keypad.getKey();
    if (key >= '0' && key <= '9') {
      idString += key;        // Append valid digit to the ID string
      Serial.print(key);      // Echo the key pressed
    } else if (key == '#') {  // Use '#' to confirm input
      break;                  // Exit loop when '#' is pressed
    }
    delay(100);  // Small delay to prevent excessive CPU usage
  }

  if (idString.length() > 0) {
    return idString.toInt();  // Convert string to int
  }
  return 0;  // Default to 0 if no valid input
}

int waitForProductSelection() {
  char key = 0;
  while (true) {
    key = keypad.getKey();
    if (key >= '1' && key <= '9') {
      int userInput = key - '0';  // Convert char to int
      if (userInput <= productCount) {
        return userInput - 1;  // Return the index of the selected product
      } else {
        Serial.println("Invalid selection. Please enter a valid product number.");
      }
    }
    delay(100);  // Small delay to prevent excessive CPU usage
  }
}

char waitForSizeSelection() {
  char key = 0;

  // Print the prompt once at the beginning
  Serial.println("Select size (1 = S, 3 = M, 4 = L, 6 = XL):");

  while (true) {
    key = keypad.getKey();
    if (key == '1' || key == '3' || key == '4' || key == '6') {
      char size;
      switch (key) {
        case '1':
          size = 'S';
          break;
        case '3':
          size = 'M';
          break;
        case '4':
          size = 'L';
          break;
        case '6':
          size = 'X';
          break;
      }
      return size;               // Return the selected size
    } else if (key != NO_KEY) {  // Check if a key was pressed
      Serial.println("Invalid size. Please enter 1, 3, 4, or 6.");
    }
    delay(100);  // Small delay to prevent excessive CPU usage
  }
}

int waitForQuantityInput() {
  char key = 0;
  int quantity = 0;

  while (true) {
    Serial.println("Enter quantity (1 or 2):");
    while (true) {
      key = keypad.getKey();
      if (key == '1' || key == '2') {
        quantity = key - '0';  // Convert char to int
        break;                 // Exit inner loop if valid key is pressed
      }
      delay(100);  // Small delay to prevent excessive CPU usage
    }

    // Validate the quantity
    if (quantity >= 1 && quantity <= 2) {
      return quantity;
    } else {
      Serial.println("Invalid quantity. Please enter 1 or 2.");
    }
  }
}

void sendOrderDetails(int studentId, int orderCount) {
  if (client.connect(server, port)) {
    Serial.println("Connected to server");

    // Create JSON string for order details
    String jsonOrderDetails = "{\"student_id\":" + String(studentId) + ",\"order_details\":[";
    for (int i = 0; i < orderCount; i++) {
      if (i > 0) jsonOrderDetails += ",";
      jsonOrderDetails += "{\"product_id\":" + String(orders[i].product_id) + ",\"size\":\"" + orders[i].size + "\",\"quantity\":" + String(orders[i].quantity) + "}";
    }
    jsonOrderDetails += "]}";

    Serial.print("jsonOrderDetails: ");
    Serial.println(jsonOrderDetails);

    // Send the HTTP POST request
    client.println("POST /vm-unif/main-content/insert_order.php HTTP/1.1");
    client.println("Host: 192.168.1.22");
    client.println("Connection: close");
    client.println("Content-Type: application/json");
    client.print("Content-Length: ");
    client.println(jsonOrderDetails.length());
    client.println();
    client.println(jsonOrderDetails);

    // Wait for server response
    while (client.connected() && !client.available()) {
      delay(10);
    }

    // Read and print the response
    String response = "";
    while (client.available()) {
      response += client.readStringUntil('\n');
    }
    Serial.println("Server response:");
    Serial.println(response);

    // Close the connection
    client.stop();
  } else {
    Serial.println("Connection failed");
  }
}
