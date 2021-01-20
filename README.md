# runner-staging-test

[![Build Status](https://travis-ci.com/keboola/runner-config-test.svg?branch=master)](https://travis-ci.com/keboola/runner-config-test)

Component for testing Runner configuration interactions. 
> :warning: This component is not for production use, it is deployed only to testing.

# Usage
Create the following configuration.

```json
    {             
        "storage": {
            "parameters": {
                "operation": "list",
            }
        }
    }
```

Valid values for operation are:
- `list` -- list files in input mapping folders
- `dump-config` -- print the config.json file
- `unsafe-dump-config` -- print the config file including secrets 
- `sleep` -- sleep for a number of seconds given in the `timeout` parameter

Run the component php src\run.php.

## Development
 
Clone this repository.

Run the test suite using this command:

```
docker-compose run --rm dev composer tests
```
 
# Integration

For information about deployment and integration with KBC, please refer to the [deployment section of developers documentation](https://developers.keboola.com/extend/component/deployment/) 
