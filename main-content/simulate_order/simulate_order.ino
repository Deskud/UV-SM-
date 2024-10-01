#include <Ethernet.h>
#include <SPI.h>
#include <ArduinoJson.h>

// Network settings
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
IPAddress ip(192, 168, 1, 100);  // Arduino IP
IPAddress server(192, 168, 1, 22);  // Server IP
int port = 80;  // HTTP port

EthernetClient client;

// Button setup
const int buttonPin = 2;
int buttonState = 0;

// To hold product data
struct Product {
  String name;
  int quantity;
};
Product products[24];  // Adjust size based on maximum number of products
int productCount = 0;

void setup() {
  Serial.begin(9600);

  // Initialize Ethernet
  Ethernet.begin(mac, ip);
  delay(1000);
  
  // Initialize the button pin as input
  pinMode(buttonPin, INPUT);
}

void loop() {
  // Read the button state
  buttonState = digitalRead(buttonPin);
  
  // Check if the button is pressed
  if (buttonState == HIGH) {
    // Debouncing the button
    delay(50);
    if (digitalRead(buttonPin) == HIGH) {
      Serial.println("Button pressed, fetching data...");
      fetchProducts();
      
      // Wait for the button to be released
      while (digitalRead(buttonPin) == HIGH);
      delay(50);  // Debouncing after release
    }
  }

  // Check if user input is available for product selection
  if (Serial.available() > 0) {
    int userInput = Serial.parseInt();  // Read the user input
    if (userInput > 0 && userInput <= productCount) {
      Serial.print("userInput: ");
      Serial.println(userInput);
      Serial.print("You selected: ");
      Serial.println(products[userInput - 1].name);
    } else {
      Serial.println("Invalid selection. Please enter a valid product number.");
    }
  }

  // Other tasks here
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
    
    // Detect end of headers (an empty line signals the end of headers)
    if (line == "\r") {
      isBody = true;
      continue;
    }
    
    // If headers are done, start collecting the JSON response
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
  // Check if jsonResponse is not empty
  if (jsonResponse.length() == 0) {
    Serial.println("JSON response is empty.");
    return;
  }

  // Estimate size based on expected data
  const size_t capacity = JSON_ARRAY_SIZE(24) + 24 * JSON_OBJECT_SIZE(2) + 24 * 50;
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
    if (product.containsKey("product_name") && product.containsKey("product_quantity")) {
      const char* product_name = product["product_name"];
      int quantity = product["product_quantity"];
      
      // Store the product in the array
      products[productCount].name = product_name;
      products[productCount].quantity = quantity;
      productCount++;

      // Print product with index for selection
      Serial.print(index);
      Serial.print(". Product: ");
      Serial.print(product_name);
      Serial.print(", Quantity: ");
      Serial.println(quantity);
      index++;
    } else {
      Serial.println("Unexpected JSON format.");
    }
  }

  // Ask the user to select a product
  if (productCount > 0) {
    Serial.println("Please select a product by entering the corresponding number.");
  } else {
    Serial.println("No products available.");
  }
}
