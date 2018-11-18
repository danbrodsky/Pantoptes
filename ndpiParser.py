import sys, os
import subprocess
import time
import json
import requests

def exception(fun):
    def wrapper(self, *args, **kwargs):
        try:
            return fun(self, *args, **kwargs)
        except Exception as e:
            print(e)
    return wrapper

@exception
def getFlows(timeout, captureFile, jsonFile):

    subprocess.Popen(['sudo','timeout', str(timeout), 'tcpdump', '-w', captureFile], stdout=subprocess.PIPE)
    time.sleep(int(timeout) + 1)

    subprocess.Popen(['ndpiReader','-v', '2', '-i', captureFile, '-j', jsonFile, '|', 'cat', 'jsonFile'], stdout=subprocess.PIPE)

    return
@exception
def readDataFromJSON(jsonFile):
     with open(jsonFile, 'r') as jf:
         data=jf.read().replace('\n', '')
         return data

@exception
def parseFlows(data):
    parsedFlows = []
    print(data)
    flows = json.loads(data)['known.flows']
    for flow in flows:
         parsedFlows.append({ "NodeIP": "TODO: Add this", "NodeID": "TODO: Add this", "Tool": "ndpi", "SourceIP": flow['host_a.name'], "SourcePort": int(flow['host_a.port']), "DestinationIP": flow['host_b.name'], "DestinationPort": int(flow['host_b.port']), "Type": flow['detected.protocol.name'] })
    return parsedFlows

@exception
def sendFlowsToServer(postUrl, parsedFlows):
    for flow in parsedFlows:
        jsonFlow = json.dumps(flow)
        print(jsonFlow)
        r = requests.post(postUrl, data=jsonFlow)
        print(r.json())
    return

if __name__ == '__main__':
    if len(sys.argv) != 5:
        print('Expected 4 arguments: timeout, capture file name, json file name, and post URL')
        sys.exit()
    while 1:
        timeout = sys.argv[1]
        captureFile = sys.argv[2]
        jsonFile = sys.argv[3]
        postUrl = sys.argv[4]

        getFlows(timeout, captureFile, jsonFile)

        data = readDataFromJSON(jsonFile)

        parsedFlows = parseFlows(data)

        sendFlowsToServer(postUrl, parsedFlows)

