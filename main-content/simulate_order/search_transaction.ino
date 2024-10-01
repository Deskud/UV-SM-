#include <SPI.h>
#include <Ethernet.h>
#include <ArduinoJson.h>
#include <Servo.h>

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
EthernetClient client;
IPAddress server(192, 168, 1, 22);
int port = 80;

struct Item {
  String productName;
  int quantity;
  int cell_num;  // Changed index to cell_num
};

const int maxItems = 10;
Item items[maxItems];
int itemCount = 0;

String qrCode;
bool matchFound = false;
const int irSensorPin = 11;

const int numServos = 12;  // Updated for 12 servos
Servo servos[numServos];
int servoPins[numServos] = {22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33}; // Update with actual pins

void setup() {
  Serial.begin(9600);
  Ethernet.begin(mac);
  for (int i = 0; i < numServos; i++) {
    servos[i].attach(servoPins[i]);
  }
  while (!Serial) {}
}

void loop() {
  if (Serial.available()) {
    qrCode = Serial.readStringUntil('\n');
    qrCode.trim();
    Serial.print("Scanned QR Code: ");
    Serial.println(qrCode);

    if (client.connect(server, port)) {
      client.print("GET /vm-unif/main-content/search_transaction.php?qrcode=");
      client.print(qrCode);
      client.println(" HTTP/1.1");
      client.println("Host: 192.168.1.100");
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
              response = "";
            } else {
              response += c;
            }
          } else {
            response += c;
          }
        }
      }

      Serial.println("Raw Server Response: ");
      Serial.println(response);

      int jsonStart = response.indexOf('{');
      if (jsonStart != -1) {
        String jsonData = response.substring(jsonStart);
        jsonData.trim();

        if (jsonData.startsWith("{")) {
          StaticJsonDocument<512> doc;
          DeserializationError error = deserializeJson(doc, jsonData);
          if (!error) {
            const char* status = doc["status"];
            if (strcmp(status, "success") == 0) {
              matchFound = true;
              Serial.println("Transaction found!");
              itemCount = 0;
              JsonArray itemArray = doc["items"];
              Serial.println("Items for dispensing:");
              for (JsonObject item : itemArray) {
                if (itemCount < maxItems) {
                  const char* productName = item["product_name"];
                  int quantity = item["quantity"];
                  int cell_num = item["cell_num"]; // Retrieve cell_num
                  items[itemCount] = { String(productName), quantity, cell_num };
                  itemCount++;
                  Serial.print("Item: ");
                  Serial.print(productName);
                  Serial.print(", Quantity: ");
                  Serial.print(quantity);
                  Serial.print(", Cell Number: ");
                  Serial.println(cell_num);
                }
              }
              Serial.println("Ready for dispensing.");
              dispenseItems();
            } else {
              Serial.println("Transaction not found or already processed.");
            }
          } else {
            Serial.print("Error parsing server response: ");
            Serial.println(error.c_str());
          }
        } else {
          Serial.println("Server response does not start with JSON object.");
        }
      } else {
        Serial.println("No JSON data found in server response.");
      }
      client.stop();
    } else {
      Serial.println("Connection to server failed.");
    }
  }
}

void dispenseItems() {
  for (int i = 0; i < itemCount; i++) {
    int sensorValue;
    Serial.print("Dispensing Item Cell Number: ");
    Serial.println(items[i].cell_num);
    Serial.print("Item Name: ");
    Serial.println(items[i].productName);
    Serial.print("Quantity: ");
    Serial.println(items[i].quantity);
    
    for (int j = 0; j < items[i].quantity; j++) {
      sensorValue = digitalRead(irSensorPin);
      Serial.print("IR sensor value: ");
      Serial.println(sensorValue);
      if (sensorValue == 1) {
        Serial.println("Dispensing...");
        servos[items[i].cell_num].write(0);  // Rotate servo corresponding to cell_num
      } else if (sensorValue == 0) {
        while (sensorValue == 0) {
          Serial.print("Item dispensed. Please wait...");
          servos[items[i].cell_num].write(90);
          sensorValue = digitalRead(irSensorPin);
        }
      }
      delay(1000);  // Wait before dispensing next item
    }
  }
}
