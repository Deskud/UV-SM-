#include <SPI.h>
#include <Ethernet.h>
#include <ArduinoJson.h>
#include <Servo.h>
#include <SoftwareSerial.h>
#include <LiquidCrystal_I2C.h>
SoftwareSerial qrscanner(11, 12);
LiquidCrystal_I2C lcd(0x27, 20, 4);

// Network settings
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
IPAddress ip(192, 168, 1, 105);    // Arduino IP
IPAddress server(192, 168, 1, 2);  // Server IP
int port = 80;                     // HTTP port

EthernetClient client;

// Structure to hold item information
struct Item {
  int product_id;
  String productName;
  String sizeName;
  int unit_num;
  int quantity;
  int index;
};
const int maxItems = 5;
Item items[maxItems];
int itemCount = 0;
int transaction_id;
int order_id;
// const int irSensorPin = 3;
const int trigPin = 3;
const int echoPin = 4;
const int numServos = 12;
Servo servos[numServos];
int servoPins[numServos] = { 22, 25, 27, 29, 31, 33, 35, 37, 39, 41, 43, 45 };

void setup() {
  Serial.begin(9600);
  lcd.init();
  lcd.backlight();
  qrscanner.begin(9600);
  Ethernet.begin(mac, ip);
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);

  for (int i = 0; i < numServos; i++) {
    servos[i].attach(servoPins[i]);
  }

  while (!Serial) { ; }  // Wait for serial connection
}

void loop() {
  lcd.clear();
  Serial.println("Scan QR code here to claim transaction...");
  displayText(0, 0, "Scan QR code here to");
  displayText(0, 1, "claim transaction...");

  String qrCode = "";
  while (true) {
    if (qrscanner.available())  // Check if there is Incoming Data in the Serial Buffer.
    {
      qrCode = "";
      while (qrscanner.available())  // Keep reading Byte by Byte from the Buffer till the Buffer is empty
      {
        char input = qrscanner.read();  // Read 1 Byte of data and store it in a character variable
        qrCode += input;                // Append the byte to the QR code string
        delay(5);                       // A small delay
      }
      qrCode.trim();
      Serial.println("QR Code Data: " + qrCode);  // Print the QR code data
      displayText(0, 2, "QR Code Data:");
      displayText(2, 3, qrCode);
      delay(1500);
      break;  // Exit the loop when a QR code value is scanned
    }
  }

  // Attempt to connect to the server
  if (connectToServer()) {
    lcd.clear();
    displayText(0, 0, "Connecting to");
    displayText(0, 1, "server...");
    processServerResponse(qrCode);
  } else {
    Serial.println("Connection to server failed.");
    lcd.clear();
    displayText(0, 0, "Connection to");
    displayText(0, 1, "server failed.");
    delay(2000);
    lcd.clear();
    displayText(0, 0, "Please go to the");
    displayText(0, 1, "Treasury or");
    displayText(0, 2, "Purchasing office.");
    delay(2000);
    lcd.clear();
    displayText(0, 0, "Sorry for the");
    displayText(0, 1, "inconvenience...");
    delay(2000);
  }
  delay(100);
}

void displayText(int x, int y, String text) {
  lcd.setCursor(x, y);
  lcd.print(text);
}

bool connectToServer() {
  Serial.println("Connecting to server...");
  if (client.connect(server, port)) {
    Serial.println("Connected to server.");
    return true;
  }
  return false;
}

void processServerResponse(String qrCode) {
  String encodedQrCode = urlEncode(qrCode);
  client.print("GET /uvm/arduino/arduino-scripts/search_transaction.php?qrcode=");
  client.print(encodedQrCode);
  client.println(" HTTP/1.1");
  client.print("Host: ");
  client.println(server);
  client.println("Connection: close");
  client.println();

  String response;
  bool headersEnded = false;

  while (client.connected() || client.available()) {
    if (client.available()) {
      char c = client.read();
      if (!headersEnded) {
        if (c == '\n' && response.endsWith("\r\n\r\n")) {
          headersEnded = true;
          response = "";  // Clear any remaining header content
        } else {
          response += c;
        }
      } else {
        response += c;  // Accumulate JSON data
      }
    }
  }

  Serial.println("Raw Server Response: ");
  Serial.println(response);

  int jsonStart = response.indexOf('{');
  if (jsonStart != -1) {
    String jsonData = response.substring(jsonStart);
    parseJsonResponse(jsonData);
  } else {
    Serial.println("No JSON data found in server response.");
  }

  client.stop();  // Close the connection
}

void parseJsonResponse(String jsonData) {
  if (jsonData.startsWith("{")) {
    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, jsonData);

    if (!error) {
      const char* status = doc["status"];
      if (strcmp(status, "success") == 0) {
        Serial.println("Transaction found!");
        itemCount = 0;  // Reset item count
        transaction_id = doc["transaction_id"];
        order_id = doc["order_id"];
        Serial.println("Transaction ID: ");
        Serial.println(transaction_id);
        Serial.println("Order ID: ");
        Serial.println(order_id);

        JsonArray itemArray = doc["items"];
        Serial.println("Items to dispense:");
        lcd.clear();
        displayText(0, 0, "Items to dispense:");

        for (JsonObject item : itemArray) {
          if (itemCount < maxItems) {
            const int productId = item["product_id"];
            const char* productName = item["product_name"];
            const char* sizeName = item["size_name"];
            int unit_num = item["unit_num"];
            int quantity_remaining = item["quantity_remaining"];  // Updated field

            items[itemCount] = { productId, productName, sizeName, unit_num, quantity_remaining, itemCount };
            itemCount++;

            Serial.print(" | Item: ");
            Serial.print(productName);
            Serial.print("Size: ");
            Serial.print(sizeName);
            Serial.print(" | Unit: ");
            Serial.print(unit_num);
            Serial.print(" | Quantity Remaining: ");
            Serial.print(quantity_remaining);
            Serial.println();
            lcd.clear();
            displayText(0, 1, "Name: " + String(productName));
            displayText(0, 2, "Size: " + String(sizeName));
            displayText(0, 3, "Qty Remaining: " + String(quantity_remaining));
            delay(2000);
          }
        }
        Serial.println("Ready to dispense...");
        lcd.clear();
        displayText(0, 0, "Ready to dispense...");
        dispenseItems();
      } else if (strcmp(status, "error") == 0 && doc["message"] == "Transaction was already processed.") {
        // Handle fully claimed transaction
        Serial.println("Transaction was already processed.");
        lcd.clear();
        displayText(0, 0, "Transaction was");
        displayText(0, 1, "already processed.");
        delay(2000);
        lcd.clear();
      } else {
        Serial.println("Transaction not found.");
        lcd.clear();
        displayText(0, 0, "Transaction");
        displayText(0, 1, "not found.");
        delay(2000);
        lcd.clear();
        displayText(0, 0, "Please go to the");
        displayText(0, 1, "Treasury or");
        displayText(0, 2, "Purchasing office.");
        delay(2000);
        lcd.clear();
        displayText(0, 0, "Sorry for the");
        displayText(0, 1, "inconvenience...");
        delay(2000);
      }
    } else {
      Serial.print("Error parsing server response: ");
      Serial.println(error.c_str());
    }
  } else {
    Serial.println("Server response does not start with JSON object.");
  }
}

int itemDrop() {
  long duration, distance;

  // Clear the TRIG pin
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);

  // Set the TRIG pin HIGH for 10 microseconds
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  // Read the ECHO pin
  duration = pulseIn(echoPin, HIGH);

  // Calculate distance in cm
  distance = duration * 0.0343 / 2;

  // Print distance to Serial Monitor
  Serial.print("Distance: ");
  Serial.print(distance);
  Serial.println(" cm");

  // Condition to control LED
  if (distance > 72) {
    return 1;
  } else {
    return 0;  // Turn off LED
  }
}

void dispenseItems() {
  int sensorValue;

  for (int i = 0; i < itemCount; i++) {
    if (items[i].quantity <= 0) {
      Serial.println("No quantity to dispense for this item.");
      continue;  // Skip dispensing if quantity is zero
    }

    Serial.print("Dispensing on cell: ");
    Serial.println(items[i].unit_num);
    Serial.print("Item Name: ");
    Serial.print(items[i].productName);
    Serial.print(items[i].sizeName);
    Serial.print("Quantity: ");
    Serial.println(items[i].quantity);
    lcd.clear();
    displayText(0, 0, "Dispensing item:");
    displayText(0, 1, items[i].productName + " (" + items[i].sizeName + ")");
    displayText(0, 2, "Qty: " + String(items[i].quantity));

    int j = 0;
    int servoIndex = items[i].unit_num - 1;
    displayText(0, 3, "dispensed: " + String(j));

    while (j < items[i].quantity) {
      sensorValue = itemDrop();

      if (sensorValue == 1) {
        servos[servoIndex].write(0);  // Activate servo
        delay(50);
      } else if (sensorValue == 0) {
        while (sensorValue == 0) {
          servos[servoIndex].write(90);  // Reset servo
          delay(50);
          sensorValue = itemDrop();
        }
        j++;
        displayText(0, 3, "dispensed: " + String(j));
        updateStockOnServer(order_id, items[i].product_id, 1);
        delay(1000);
      }
      delay(50);  // Wait before dispensing next item
    }
    servos[servoIndex].write(90);  // Reset servo to neutral after each item
    delay(1000);                   // Wait before dispensing next item
  }
  lcd.clear();
  displayText(0, 0, "Dispense completed!");
  displayText(0, 1, "Thank you!");
  delay(2000);
}

String urlEncode(const String& str) {
  String encoded = "";
  for (size_t i = 0; i < str.length(); i++) {
    char c = str.charAt(i);
    if (isalnum(c) || c == '-' || c == '_' || c == '.' || c == '~') {
      encoded += c;  // Safe characters
    } else {
      encoded += '%' + String(c, HEX);
    }
  }
  return encoded;
}

void updateStockOnServer(const int order_id, const int product_id, int quantity) {
  if (connectToServer()) {
    client.print("GET /uvm/arduino/arduino-scripts/update_stock.php?order_id=");
    client.print(order_id);
    client.print("&product_id=");
    client.print(product_id);
    client.print("&quantity=");
    client.println(quantity);
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println("Connection: close");
    client.println();

    // Read the server's response (optional, you can log it if needed)
    String response;
    while (client.connected() || client.available()) {
      if (client.available()) {
        response += (char)client.read();
      }
    }
    Serial.println("Stock update response: ");
    Serial.println(response);

    client.stop();  // Close the connection
  } else {
    Serial.println("Failed to connect to server for stock update.");
  }
}
