# idxboost-translations

## Requirements...
```
 PHP: 7.4.19
 MySql: 5.7.33
```

## How to start...

```
composer install
```

```
php bin/console doctrine:database:create
```

```
php bin/console doctrine:schema:create
```

```
php bin/console doctrine:schema:validate
```

## Start a local server using symfony cli...

```
symfony server:start
```
## Documentation 
```
https://editor.swagger.io/
```

 ```
  openapi: 3.0.3
  info:
    title: Translations 3.0
    
    version: 1.0.11
  servers:
    - url: http://127.0.0.1:8000
  tags:
    - name: Translations
      description: Everything about your Translations
  paths:
    /api/translation/translations_by_application/{applicationId}:
      get:
        tags:
          - Translations
        summary: Find translations by applicationId
        description: Returns a list of translations
        operationId: getTranslationsByApplicationId
        parameters:
          - name: applicationId
            in: path
            description: ID of aplication to return
            required: true
            schema:
              type: integer
              format: int64
        responses:
          '200':
            description: successful operation
            content:
             application/json:
              schema:
                  type: array
                  items:
                    $ref: '#/components/schemas/Translation'
             application/xml:
                schema:
                  type: array
                  items:
                    $ref: '#/components/schemas/Translation'
          '400':
            description: Invalid ID supplied
          '404':
            description: Pet not found
        
      
  components:
    schemas:
      Translation:
        required:
          - language
        type: object
        properties:
          language:
            type: string
            example: 'es'
        xml:
          name: translation
    

 ```