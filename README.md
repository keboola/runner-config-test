# runner-staging-test

[![Build Status](https://travis-ci.com/keboola/runner-staging-test.svg?branch=master)](https://travis-ci.com/keboola/runner-staging-test)

Component for testing Runner input mapping from ABS and S3 staging storage. 
> :warning: This component is not for production use, it is deployed only to testing.

# Usage

Create a configuration with S3 or ABS input stage and input mapping, operation and optionally filename.
Set `operation` parameter either to "list" to list the filenames of the input manifests.
Set `operation` parameter either to "content" to dump the content of the manifest specified by `filename`.

```json
    {             
        "storage": {
            "parameters": {
                "operation": "content",
                "filename": "my-file"            
            },
            "input": {        
                "files": [
                    {
                        "source": "test-file",
                        "destination": "my-file"
                    }
                ]     
            }   
        }
    }
```

Run the component php src\run.php.

## Development
 
Clone this repository.

Run the test suite using this command:

```
docker-compose run --rm dev composer tests
```
 
# Integration

For information about deployment and integration with KBC, please refer to the [deployment section of developers documentation](https://developers.keboola.com/extend/component/deployment/) 
