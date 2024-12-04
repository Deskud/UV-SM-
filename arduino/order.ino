#include <Ethernet.h>
#include <SPI.h>
#include <ArduinoJson.h>
#include <Keypad.h>
#include <SoftwareSerial.h>
#include <Adafruit_GFX.h>
#include <MCUFRIEND_kbv.h>
MCUFRIEND_kbv tft;
SoftwareSerial printer(17, 18);

#define BLACK 0x0000
#define WHITE 0xFFFF
#define RED 0xF800
#define GREEN 0x07E0
#define BLUE 0x001F
#define YELLOW 0xFFE0
#define LIGHT_BLUE 0xADD8E6

// Network settings
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
int port = 80;
IPAddress server(192, 168, 1, 4);

EthernetClient client;

const byte ROWS = 4;
const byte COLS = 4;

char keys[ROWS][COLS] = {
  { '1', '4', '7', '*' },
  { '2', '5', '8', '0' },
  { '3', '6', '9', '#' },
  { 'A', 'B', 'C', 'D' }
};
byte rowPins[ROWS] = { 36, 34, 32, 30 };
byte colPins[COLS] = { 28, 26, 24, 22 };
Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);

unsigned long lastActivityTime = 0;           // To track the last time of user interaction
const unsigned long timeoutDuration = 60000;  // 60 seconds timeout

typedef String (*GetNameFunc)(void* item);

// to hold product data
struct Product {
  String product_name;
};
const int productNum = 12;
Product products[productNum];
int productCount = 0;

// to hold product size data
struct Size {
  int product_id;
  int size_id;
  String size_name;
};
const int sizeNum = 3;
Size sizes[sizeNum];
int sizeCount = 0;

// to hold order details data
struct Order {
  int product_id;
  String prod_name;
  int size;
  String prod_size;
  int quantity;
};
const int orderNum = 2;
Order orders[orderNum];
int orderCount = 0;

int product_quantity;

void setup(void) {
  Serial.begin(9600);
  printer.begin(9600);
  Ethernet.begin(mac);
  tft.begin(tft.readID());
  tft.setRotation(0);
  tft.fillScreen(BLACK);
  tft.setTextColor(WHITE);
  tft.setTextSize(2);
}

void loop() {
  // Wait for input to start fetching products
  tft.fillScreen(BLACK);
  displayCenteredText("ARDUINO-BASED", 80, 3, WHITE);
  displayCenteredText("UNIFORM\nVENDING\nMACHINE\n", 110, 6, YELLOW);
  Serial.println("Press 'A' to start...");
  displayText(30, 420, "Press 'A' to start...", 2, WHITE);
  keyToStart('A');
  orderCount = 0;
  tft.fillScreen(BLACK);
  displayText(30, 30, "Reminder: ", 3, YELLOW);
  displayText(30, 80, "Each order can\nonly contain up\nto 2 items and\nlimited to\n2pcs each.", 2, WHITE);
  delay(3000);

  while (orderCount < orderNum) {
    int selectedProductIndex = productSelection();
    Serial.println("selectedProductIndex: " + String(selectedProductIndex));

    int selectedSizeIndex = sizeSelection(selectedProductIndex);
    Serial.println("selectedSizeIndex: " + String(selectedSizeIndex));

    int selectedQuantity = quantitySelection(sizes[selectedSizeIndex].product_id);
    Serial.println("selectedQuantity: " + String(selectedQuantity));

    orders[orderCount].product_id = sizes[selectedSizeIndex].product_id;
    orders[orderCount].prod_name = products[selectedProductIndex].product_name;
    orders[orderCount].size = sizes[selectedSizeIndex].size_id;
    orders[orderCount].prod_size = sizes[selectedSizeIndex].size_name;
    orders[orderCount].quantity = selectedQuantity;

    Serial.print("PID: ");
    Serial.println(orders[orderCount].product_id);
    Serial.print("PName: ");
    Serial.println(orders[orderCount].prod_name);
    Serial.print("SID: ");
    Serial.println(orders[orderCount].size);
    Serial.print("SName: ");
    Serial.println(orders[orderCount].prod_size);
    Serial.print("Quan: ");
    Serial.println(orders[orderCount].quantity);

    tft.fillScreen(BLACK);
    displayText(30, 30, "Item " + String(orderCount + 1) + ":", 3, WHITE);
    displayText(30, 80, "Name: " + orders[orderCount].prod_name, 2, WHITE);
    displayText(30, 110, "Size: " + orders[orderCount].prod_size, 2, WHITE);
    displayText(30, 140, "Qty: " + String(orders[orderCount].quantity) + "pc(s).", 2, WHITE);
    orderCount++;

    if (orderCount < orderNum) {
      displayText(30, 390, "Add another item?\n'A' = Yes | 'B' = No", 2, WHITE);
      char key = keypad.getKey();
      while (key != 'A' && key != 'B') {
        systemTimeout();
        key = keypad.getKey();
        delay(100);
      }
      if (key == 'B') break;  // Exit loop if user does not want to add more
      lastActivityTime = millis();
    }
  }

  tft.fillScreen(BLACK);
  displayText(30, 30, "Saving order...", 2, YELLOW);
  sendOrderDetails(orderCount);  // Send order details to the server

  displayText(30, 90, "Done!", 2, GREEN);
  delay(1000);

  tft.fillScreen(BLACK);
  displayText(30, 30, "Take your receipt\nand bring it\nto the cashier.", 2, WHITE);
  delay(2000);
  tft.fillScreen(BLACK);
  displayText(30, 30, "Thank you!", 3, YELLOW);
  delay(2000);
}

void systemTimeout() {
  if (millis() - lastActivityTime >= timeoutDuration) {
    tft.fillScreen(BLACK);
    displayText(30, 30, "Session timed out.\nReturning to start...", 2, WHITE);
    asm volatile("  jmp 0");
  }
}

void keyToStart(char expectedKey) {
  char key = 0;
  while (key != expectedKey) {
    key = keypad.getKey();
    delay(100);
  }
}

void displayText(int x, int y, String text, int size, uint16_t color) {
  tft.setTextColor(color);
  tft.setTextSize(size);

  int lineHeight = size * 15;
  int currentY = y;

  String currentLine = "";

  // Iterate through the text and handle newlines
  for (int i = 0; i < text.length(); i++) {
    char c = text.charAt(i);

    if (c == '\n') {
      // Print the current line and move to the next line
      tft.setCursor(x, currentY);
      tft.print(currentLine);
      currentY += lineHeight;
      currentLine = "";  // Clear the current line buffer
    } else {
      currentLine += c;  // Add the character to the current line
    }
  }

  // Print the last line if it exists
  if (currentLine.length() > 0) {
    tft.setCursor(x, currentY);
    tft.print(currentLine);
  }
}

void displayCenteredText(String text, int y, int textSize, uint16_t color) {
  tft.setTextSize(textSize);
  tft.setTextColor(color);

  int16_t x1, y1;
  uint16_t width, height;

  int lineHeight = textSize * 10;  // Approximate height of one line

  // Split and print each line based on the starting y position
  String currentLine = "";
  for (unsigned int i = 0; i < text.length(); i++) {
    if (text[i] == '\n' || i == text.length() - 1) {
      if (i == text.length() - 1) currentLine += text[i];  // Add last char

      // Get text bounds for the current line
      tft.getTextBounds(currentLine, 0, y, &x1, &y1, &width, &height);

      // Calculate x to center the text
      int16_t x = (tft.width() - width) / 2;

      // Set cursor and print the current line
      tft.setCursor(x, y);
      tft.print(currentLine);

      // Move the cursor down for the next line
      y += lineHeight;

      currentLine = "";  // Reset for the next line
    } else {
      currentLine += text[i];
    }
  }
}

void fetchData(String endpoint, int selectedId) {
  int retries = 3;
  bool connected = false;

  while (retries > 0 && !connected) {
    // If not connected, try to reconnect
    if (client.connected()) {
      connected = true;
    } else if (client.connect(server, port)) {
      connected = true;  // Set the flag if connected
      Serial.println("Connected to server");
    } else {
      retries--;
      Serial.println("Connection failed, retrying...");
      tft.fillScreen(BLACK);
      displayText(30, 30, "Connection failed.\nRetrying...", 2, WHITE);
      delay(1000);
    }
  }

  if (!connected) {
    Serial.println("Failed to connect after retries");
    tft.fillScreen(BLACK);
    displayText(30, 30, "Failed to connect.\nPlease go to the\nTreasury or \nPurchasing office.", 2, WHITE);
    delay(2000);
    tft.fillScreen(BLACK);
    displayText(30, 30, "Sorry for\nthe inconvenience.", 2, WHITE);
    delay(2000);
    asm volatile("  jmp 0");  // Reset or halt program
  }

  // Prepare and send the GET request
  if (endpoint == "/uvm/arduino/arduino-scripts/get_sizes.php") {
    String encodedProductName = urlencode(products[selectedId].product_name);
    endpoint += "?product_name=" + encodedProductName;
  } else if (endpoint == "/uvm/arduino/arduino-scripts/get_quantities.php") {
    endpoint += "?product_id=" + String(selectedId);
  }

  // Send the GET request
  client.print("GET ");
  client.print(endpoint);
  client.println(" HTTP/1.1");
  client.print("Host: ");
  client.println(server);
  client.println("User-Agent: Arduino/1.0");
  client.println("Connection: close");
  client.println();

  // Wait for the server's response
  String jsonResponse = "";
  bool isBody = false;

  unsigned long startMillis = millis();  // Timeout timer
  while (client.connected() && !client.available()) {
    if (millis() - startMillis > 5000) {  // Timeout after 5 seconds if no data
      Serial.println("Timeout waiting for server response.");
      break;
    }
    delay(10);  // Small delay to prevent locking up the processor
  }

  // Read the server's response if available
  while (client.available()) {
    String line = client.readStringUntil('\n');

    if (line == "\r") {
      isBody = true;
      continue;
    }

    if (isBody) {
      jsonResponse += line;
    }
  }

  client.stop();  // Close the connection
  Serial.println("Disconnected from server");
  Serial.println("Raw JSON Response:");
  Serial.println(jsonResponse);

  // Parse and process the response
  parseJsonData(jsonResponse, endpoint);
}

void parseJsonData(String jsonResponse, String endpoint) {
  if (jsonResponse.length() == 0) {
    Serial.println("JSON response is empty.");
    return;
  }

  const size_t capacity = JSON_ARRAY_SIZE(12) + 12 * JSON_OBJECT_SIZE(3) + 12 * 50;
  DynamicJsonDocument doc(capacity);
  DeserializationError error = deserializeJson(doc, jsonResponse);

  if (error) {
    Serial.print("Failed to parse JSON: ");
    Serial.println(error.c_str());
    return;
  }

  if (endpoint.startsWith("/uvm/arduino/arduino-scripts/get_products.php")) {
    productCount = 0;
    JsonArray productsArray = doc.as<JsonArray>();

    for (JsonObject product : productsArray) {
      products[productCount].product_name = String(product["product_name"].as<const char*>());
      productCount++;
    }
  } else if (endpoint.startsWith("/uvm/arduino/arduino-scripts/get_sizes.php")) {
    sizeCount = 0;
    JsonArray sizesArray = doc.as<JsonArray>();

    for (JsonObject size : sizesArray) {
      sizes[sizeCount].product_id = size["product_id"];
      sizes[sizeCount].size_id = size["size_id"];
      sizes[sizeCount].size_name = String(size["size_name"].as<const char*>());
      sizeCount++;
    }
  } else if (endpoint.startsWith("/uvm/arduino/arduino-scripts/get_quantities.php")) {
    product_quantity = doc[0]["product_quantity"];
  }
}

int productSelection() {
  fetchData("/uvm/arduino/arduino-scripts/get_products.php", 0);
  return handleSelection("Products", products, productCount, sizeof(Product), getProductName, 0);
}

int sizeSelection(int productIndex) {
  fetchData("/uvm/arduino/arduino-scripts/get_sizes.php", productIndex);
  return handleSelection("Sizes", sizes, sizeCount, sizeof(Size), getSizeName, 0);
}

int quantitySelection(int productId) {
  fetchData("/uvm/arduino/arduino-scripts/get_quantities.php", productId);
  int availableQuantity = product_quantity;
  return handleQuantitySelection(availableQuantity);
}

// Function to display a list of items
void displayList(const char* title, void* items, int itemCount, int itemSize, GetNameFunc getName) {
  displayText(30, 30, title, 3, WHITE);  // Display the title

  for (int i = 0; i < itemCount; i++) {
    // Use itemSize to calculate the address of the current item
    void* currentItem = static_cast<void*>(static_cast<char*>(items) + (i * itemSize));
    String itemName = getName(currentItem);     // Retrieve the name
    displayListItem(i, itemName, 80 + i * 30);  // Display the item
  }
}

// Function to display a single list item
void displayListItem(int index, String itemName, int yPosition) {
  tft.setTextSize(2);
  tft.setCursor(30, yPosition);
  tft.print(index + 1);
  tft.print(": ");
  tft.print(itemName);
}

String getProductName(void* item) {
  return static_cast<Product*>(item)->product_name;
}

String getSizeName(void* item) {
  return static_cast<Size*>(item)->size_name;
}

// Generalized selection handler function
int handleSelection(const char* title, void* items, int itemCount, int itemSize, String (*getName)(void*), int displayOffset) {
  if (!items || itemCount == 0) {
    // If there are no items to display, show an appropriate message
    tft.fillScreen(BLACK);
    displayText(30, 30, "No items available", 2, WHITE);
    displayText(30, 390, "Press any key to return", 2, WHITE);

    // Wait for a key press to return to the previous menu
    while (true) {
      char key = keypad.getKey();
      if (key) {
        tft.fillScreen(BLACK);  // Clear the screen before returning
        asm volatile("  jmp 0");
      }
      delay(100);
    }
  }

  char key = 0;
  int selectedIndex = -1;
  tft.fillScreen(BLACK);
  displayList(title, items, itemCount, itemSize, getName);
  displayText(30, 390, "Selected " + String(title) + ": " + String(selectedIndex + 1), 2, WHITE);
  lastActivityTime = millis();
  while (true) {
    systemTimeout();
    key = keypad.getKey();

    // Check if the input is valid (between '1' and the number of items)
    if (key >= '1' && (key - '0') <= itemCount) {
      selectedIndex = key - '1';  // Convert to index
      tft.fillRect(30, 390, 300, 30, BLACK);
      displayText(30, 390, "Selected " + String(title) + ": " + String(selectedIndex + 1), 2, WHITE);
    } else if (key == '#') {
      if (selectedIndex != -1) {               // Only allow exiting if a valid selection is made
        return selectedIndex + displayOffset;  // Return the selected index with offset (e.g., category_id)
      }
    } else if (key == 'D') {
      tft.fillScreen(BLACK);
      displayText(30, 30, "Cancel order?", 2, WHITE);
      displayText(30, 390, "'C' = Yes | 'D' = No", 2, WHITE);
      while (true) {
        key = keypad.getKey();
        if (key == 'C') {
          asm volatile("  jmp 0");
        } else if (key == 'D') {
          tft.fillScreen(BLACK);
          displayList(title, items, itemCount, itemSize, getName);
          break;
        }
        delay(100);
      }
    }

    // Prompt the user to select and confirm with '#'
    displayText(30, 420, "# - enter | D - cancel", 2, WHITE);
    delay(100);
  }
}

int handleQuantitySelection(int availableQuantity) {
  int selectedQuantity = 0;
  char key = 0;

  // Determine the maximum quantity allowed (minimum between 2 and the available quantity)
  int maxQuantity = min(availableQuantity, 2);

  // Initial display
  tft.fillScreen(BLACK);
  displayText(30, 30, "Quantity: " + String(product_quantity), 2, WHITE);
  displayText(30, 60, "Max: " + String(maxQuantity), 2, WHITE);
  displayText(30, 390, "Selected quantity: " + String(selectedQuantity), 2, WHITE);
  displayText(30, 420, "# - enter | D - cancel", 2, WHITE);

  while (true) {
    key = keypad.getKey();

    // If '1' is pressed, set selectedQuantity to 1
    if (key == '1' && maxQuantity >= 1) {
      selectedQuantity = 1;
      tft.fillRect(30, 390, 300, 30, BLACK);
      displayText(30, 390, "Selected quantity: " + String(selectedQuantity), 2, WHITE);

      // If '2' is pressed, set selectedQuantity to 2 (if available quantity allows it)
    } else if (key == '2' && maxQuantity >= 2) {
      selectedQuantity = 2;
      tft.fillRect(30, 390, 300, 30, BLACK);
      displayText(30, 390, "Selected quantity: " + String(selectedQuantity), 2, WHITE);

      // If '#' is pressed and a valid quantity is selected, confirm the selection
    } else if (key == '#') {
      if (selectedQuantity > 0) {  // Ensure a valid quantity has been selected
        return selectedQuantity;   // Return the selected quantity
      }
    } else if (key == 'D') {
      tft.fillScreen(BLACK);
      displayText(30, 30, "Cancel order?", 2, WHITE);
      displayText(30, 390, "'C' = Yes | 'D' = No", 2, WHITE);
      while (true) {
        key = keypad.getKey();
        if (key == 'C') {
          asm volatile("  jmp 0");
        } else if (key == 'D') {
          // Initial display
          tft.fillScreen(BLACK);
          displayText(30, 30, "Quantity: " + String(product_quantity), 2, WHITE);
          displayText(30, 60, "Max: " + String(maxQuantity), 2, WHITE);
          displayText(30, 390, "Selected quantity: " + String(selectedQuantity), 2, WHITE);
          displayText(30, 420, "# - enter | D - cancel", 2, WHITE);
          break;
        }
        delay(100);
      }
    }
    delay(100);
  }
}

void sendOrderDetails(int orderCount) {
  if (client.connect(server, port)) {
    Serial.println("Connected to server");

    // Create JSON string for order details
    String jsonOrderDetails = "{\"order_details\":[";
    for (int i = 0; i < orderCount; i++) {
      if (i > 0) jsonOrderDetails += ",";
      jsonOrderDetails += "{\"product_id\":" + String(orders[i].product_id) + ",\"quantity\":" + String(orders[i].quantity) + "}";
    }
    jsonOrderDetails += "]}";

    Serial.print("jsonOrderDetails: ");
    Serial.println(jsonOrderDetails);

    // Send the HTTP POST request
    client.println("POST /uvm/arduino/arduino-scripts/insert_order.php HTTP/1.1");
    client.print("Host: ");
    client.println(server);
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
    Serial.println("Received headers:");
    Serial.println(response);

    // Find the start of the JSON part
    int jsonIndex = response.indexOf("{");
    if (jsonIndex != -1) {
      String jsonResponse = response.substring(jsonIndex);  // Extract JSON part
      Serial.println("JSON Response:");
      Serial.println(jsonResponse);

      // Handle JSON parsing
      DynamicJsonDocument doc(2048);
      DeserializationError error = deserializeJson(doc, jsonResponse);

      if (error) {
        Serial.print("Failed to parse JSON: ");
        Serial.println(error.c_str());
        Serial.println("Received JSON: " + jsonResponse);  // Print received JSON for troubleshooting
        return;
      }

      if (doc["status"] == "success") {
        Serial.println("Order successfully placed!");
        int orderId = doc["order_id"];
        printReceipt(orderId);
      } else {
        Serial.print("Error placing order: ");
        Serial.println(doc["message"].as<String>());
      }
    } else {
      Serial.println("JSON part not found in the response.");
    }
  } else {
    Serial.println("Connection failed");
  }
}

void printReceipt(int orderId) {
  // Initialize thermal printer and print the receipt
  Serial.println("Printing receipt...");

  // Print order details
  Serial.print("Order ID: ");
  Serial.println(orderId);

  displayText(30, 60, "Order ID: " + String(orderId), 2, WHITE);
  for (int i = 0; i < orderCount; i++) {
    Serial.print(orders[i].prod_name);
    Serial.print(" (");
    Serial.print(orders[i].prod_size);
    Serial.print(") x ");
    Serial.println(orders[i].quantity);
  }
  printer.println("------------------------------");
  printer.println("    PCU - Dasmariñas Campus   ");
  printer.println("     New Academic Building    ");
  printer.println("------------------------------");
  printer.println();
  printer.print("    Order ID: ");
  printer.println(orderId);
  printer.println();
  for (int i = 0; i < orderCount; i++) {
    printer.print("     ");
    printer.print(orders[i].prod_name);
    printer.print(" (");
    printer.print(orders[i].prod_size);
    printer.print(") - ");
    printer.print(orders[i].quantity);
    printer.println("pc(s)");
  }
  printer.println();
  printer.println();
  printer.println("           Thank you!         ");
  printer.println("------------------------------");
  printer.println();
  printer.println();
  printer.println();
  printer.println();
  printer.println();
  printer.println();
}

String urlencode(const String& str) {
  String encoded = "";
  for (unsigned int i = 0; i < str.length(); i++) {
    char c = str.charAt(i);
    if (isalnum(c) || c == '-' || c == '_' || c == '.' || c == '~') {
      encoded += c;  // Add unencoded characters
    } else {
      encoded += String("%") + String(c, HEX);  // Encode others
    }
  }
  return encoded;
}