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
def getFlows(interface, timeout):

    proc = subprocess.Popen(['timeout', str(timeout), os.getcwd() + '/lpi_protoident', 'int:' + interface],stdout=subprocess.PIPE)
    time.sleep(int(timeout) + 1)

    res = str(proc.stdout.read())
    flows = [ flow for flow in res[2:].split("\\n") if len(flow) > 50 ]
    return flows

@exception
def parseFlows(flows):
    parsedFlows = []
    timestamp = time.time()

    for flow in flows:
         data = flow.split(" ")
         parsedFlows.append({ "NodeIP": "TODO: Add this", "NodeID": "TODO: Add this", "Tool": "libprotoident", "SourceIP": data[1], "SourcePort": int(data[3]), "DestinationIP": data[2], "DestinationPort": int(data[4]), "Type": data[0], "Timestamp": timestamp })
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
    if len(sys.argv) != 4:
        print('Expected 3 arguments: interface, timeout, and post URL')
        sys.exit()
    while 1:
        interface = sys.argv[1]
        timeout = sys.argv[2]
        postUrl = sys.argv[3]

        flows = getFlows(interface, timeout)

        parsedFlows = parseFlows(flows)

        sendFlowsToServer(postUrl, parsedFlows)
