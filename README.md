# runner-config-test

[![Build Status](https://travis-ci.com/keboola/runner-config-test.svg?branch=master)](https://travis-ci.com/keboola/runner-config-test)

Component for testing Runner configuration interactions. 
> :warning: This component is not for production use, it is deployed only to testing.

# Usage
Create the following configuration.

```json
    {             
        "parameters": {
            "operation": "list"
        }
    }
```

Valid values for operation are:
- `list` -- list files in input mapping folders
- `dump-config` -- print the config.json file
- `unsafe-dump-config` -- print the config file including secrets 
- `sleep` -- sleep for a number of seconds given in the `timeout` parameter
- `dump-env` -- show environment variables

You can pass arbitrary data in the `parameters.arbitrary` node.

Run the component php src\run.php.

## Sync actions
Component supports following sync actions:
* `dumpConfig` - dumps `config.json` to output
* `dumpEnv` - dumps all ENV vars to output
* `timeout` - sleeps 60 seconds, causing sync action timeout
* `emptyJsonArray` - outputs empty JSON array (`[]`)
* `emptyJsonObject` - outputs empty JSON object (`{}`)
* `invalidJson` - outputs invalid JSON
* `noResponse` - returns with no output
* `userError` - causes process to end with exit code `1`
* `applicationError` - causes process to end with exit code `2`
* `printLogs` - sends logs to GELF logger
  * requires `KBC_LOGGER_ADDR` and `KBC_LOGGER_PORT` env vars
  * supports configuration:
    ```json
      {
        "parameters": {
          "logs": {
            "transport": "udp", # udp, tcp, http
            "records": [
              {"level": "debug", "message": "debug message"},
              {"level": "critical", "message": "critical message", "context": {"extraKey": "extraVal"}}
            ]
          }
        }
      }
    ```

## Development
 
Clone this repository.

Run the test suite using this command:

```
docker-compose run --rm dev composer tests
```
 
# Integration

For information about deployment and integration with KBC, please refer to the [deployment section of developers documentation](https://developers.keboola.com/extend/component/deployment/) 

## License

MIT licensed, see [LICENSE](./LICENSE) file.
