//#include <Arduino.h>
#include <WiFi.h>
//#include <time.h>
#include <Preferences.h>
#include <AsyncTCP.h>
#include <ESPAsyncWebServer.h>
#include <SPIFFS.h>

//GLOBAL DECLARATIONS
#define _SSID "wifi_ssid"
#define _PASS "wifi_password"
#define _PORT "srv_port"
#define _BHOUR "breakfast_hour"
#define _BDURATION "breakfast_duration"
#define _LHOUR "lunch_hour"
#define _LDURATION "lunch_duration"
#define _DHOUR "dinner_hour"
#define _DDURATION "dinner_duration"


Preferences configurations;
//bool configFound = false;
//bool connectionStablished = false;

AsyncWebServer server(80);
AsyncWebSocket ws("/ws");


void processor(){

}

void notifyClients(String response) {
  ws.textAll(response);
}

void handleWebSocketMessage(void *arg, uint8_t *data, size_t len) {
  AwsFrameInfo *info = (AwsFrameInfo*)arg;
  if (info->final && info->index == 0 && info->len == len && info->opcode == WS_TEXT) {
    data[len] = 0;
    if (strcmp((char*)data, "toggle") == 0) {
      notifyClients("OK");
    }
  }
}

void onEvent(AsyncWebSocket *server, AsyncWebSocketClient *client, AwsEventType type,
             void *arg, uint8_t *data, size_t len) {
  switch (type) {
    case WS_EVT_CONNECT:
      Serial.printf("WebSocket client #%u connected from %s\n", client->id(), client->remoteIP().toString().c_str());
      break;
    case WS_EVT_DISCONNECT:
      Serial.printf("WebSocket client #%u disconnected\n", client->id());
      break;
    case WS_EVT_DATA:
      handleWebSocketMessage(arg, data, len);
      break;
    case WS_EVT_PONG:
    case WS_EVT_ERROR:
      break;
  }
}

void initWebSocket() {
  ws.onEvent(onEvent);
  server.addHandler(&ws);
}

void definePage(){
  server.on("/", HTTP_GET, [](AsyncWebServerRequest *request){
    request->send(SPIFFS, "/index.html", String(), false);
  });
  server.on("/ico/easyfeeder_Logo.ico", HTTP_GET, [](AsyncWebServerRequest *request){
    request->send(SPIFFS, "/ico/easyfeeder_Logo.ico", "image/x-icon");
  });
  server.on("/img/easyfeeder_Logo.png", HTTP_GET, [](AsyncWebServerRequest *request){
    request->send(SPIFFS, "/img/easyfeeder_Logo.png", "image/png");
  });
  server.on("/css/w3v5.css", HTTP_GET, [](AsyncWebServerRequest *request){
    request->send(SPIFFS, "/css/w3v5.css", "text/css");
  });
  server.on("/css/font-awesome.min.css", HTTP_GET, [](AsyncWebServerRequest *request){
    request->send(SPIFFS, "/css/font-awesome.min.css", "text/css");
  });
  server.on("/js/rangeinput.js", HTTP_GET, [](AsyncWebServerRequest *request){
    request->send(SPIFFS, "/js/rangeinput.js", "text/javascript");
  });
  server.on("/js/functions.js", HTTP_GET, [](AsyncWebServerRequest *request){
    request->send(SPIFFS, "/js/functions.js", "text/javascript");
  });
}

bool connectWifi() {
  bool configFound = false;
  bool connectionStablished = false;
  String wifiSSID;
  String wifiPASS;

  Serial.println("Function [connectWiFi] started.");
  if(configurations.begin("appconfig", false)) {
    wifiSSID = configurations.getString(_SSID);
    wifiPASS = configurations.getString(_PASS);
    configFound = (wifiSSID != "" && wifiPASS != "")? true : false;
    if(configFound) {
      Serial.println("Configuration has been found!!!!");
      WiFi.mode(WIFI_STA);
      WiFi.begin(wifiSSID, wifiPASS);
      Serial.print("Connecting to WiFi: ");
      Serial.print(wifiSSID);
      while (WiFi.status() != WL_CONNECTED) {
        Serial.print('.');
        delay(1000);
      }
      Serial.print(" Connected with IP: ");
      Serial.println(WiFi.localIP());
      connectionStablished = true;
    } else {
      Serial.println("Factory mode detected or configuration error has been rised:");
      Serial.println("Please connect to wifi [easyfeednet] with password [netfeedeasy] to configure your network correctly.");
    }
  } else {
    Serial.println("Unable to read Flash memory, please contact customer support.");
  }
  configurations.end();
  return (configFound && connectionStablished);
}

void startWifi() {
  Serial.print("Setting AP (Access Point)... ");
  WiFi.softAP("easyfeednet", "netfeedeasy");
  Serial.print("AP IP address: ");
  Serial.println(WiFi.softAPIP());
}
void listDir(fs::FS &fs, const char *dirname, uint8_t levels) {
  Serial.printf("Listing directory: %s\r\n", dirname);

  File root = fs.open(dirname);
  if (!root) {
    Serial.println("- failed to open directory");
    return;
  }
  if (!root.isDirectory()) {
    Serial.println(" - not a directory");
    return;
  }

  File file = root.openNextFile();
  while (file) {
    if (file.isDirectory()) {
      Serial.print("  DIR : ");
      Serial.println(file.name());
      if (levels) {
        listDir(fs, file.path(), levels - 1);
      }
    } else {
      Serial.print("  FILE: ");
      Serial.print(file.name());
      Serial.print("\tSIZE: ");
      Serial.println(file.size());
    }
    file = root.openNextFile();
  }
}


void setup() {
  Serial.begin(115200);
  delay(1000);
  if(!SPIFFS.begin(true)){
    Serial.println("SPIFFS Mount Failed");
    return;
  }
  listDir(SPIFFS, "/", 0);
  if (!connectWifi()){
    startWifi();
  }
  initWebSocket();
  definePage();
  server.begin();
}

void loop() {
  // put your main code here, to run repeatedly:

}
