# example_partners
Example connection with Yapo Api

Authentication
==============

Each request must be made with app\_id (test used in examples below) and
a hash (e.g. a request to delete an ad can look like this
<http://m.yapo.cl/api/importdeletead.json?app_id=test&hash=b56af8e790f04ccf387d47631e793f30660a84c4>).

The hash is created by concatenating the challenge given by the API and
your API-key, then hash using
[SHA1](http://en.wikipedia.org/wiki/Secure_Hash_Algorithm). Note that
only numbers 0-9 and lowercase letters a-f are used in the hash.
Uppercase letters are not valid. To get a challenge just make a request
to the api. You can use that challenge to create your hash, for a
limited time (6 minutes), and when that challenge expires a new
challenge-response is sent.

Which has this structure:

    { "authorize": { "challenge": "160900065", "status": "NOT A VALID API-KEY" } }

Each API-key has restrictions, for example you might just have access to
create new ads but not view them. If you are restricted to only create
new ads the response will look like this:

    { "authorize": { "status": "NOT AN ALLOWED APPLICATION" } }

The API-key can also be limited to ad type and category. If the ad has a
type or a category that isn\'t valid for that API-key the response will
be:

    {"[application_name]": { "status": "NOT A VALID CATEGORY OR TYPE OF AD" } }

Where application\_name is the name of service you want to reach (ex.
view, list).

If the response looks like this you are using an invalid app\_id (an id
that isn\'t registered as an authorized API user):

    { "authorize": { "status": "NOT A VALID PARTNER" } }

Applications
============

All applications need to be supplied the following two parameters, on
top of possible application-specific params:

-   app\_id - the partner name we have registered for your services
-   hash - the sha1 string explained above in the Authentication
    section.

App Newad
=========

-   **URL** - <http://m.yapo.cl/api/newad.json>
-   **Allowed request methods** - \[POST\]

See \[Data types and values section\].

Handling images
---------------

Images must be uploaded separately using the \'upload\_image\' action.
When \'upload\_image\' is called, an image\_id is returned.

These image\_ids can then be used to associate images with an ad by
including them as the parameters \'image\_id(0-N)\' when calling
\'insert\_ad\'. The first image (image\_id0) will be the main image of
the ad.

The main image is used in the ad list page etc.

Note that there is a size limit on the request body (currently at 50
Mbyte). If that limit is exceeded, the upload will be unsuccessful and
the response will have the HTTP status code [413 Request Entity Too
Large](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.14).

Newad actions
-------------

**action**

-   upload\_image - used to just upload images
-   insert\_ad - insert your ad

You can import ads if you have the option import. To import an ad you
must send import=1, and give an external\_ad\_id in string format.

If there is already an ad with the supplied external\_ad\_id, the
existing ad will be replaced by the new one. This can be used to update
existing ads.

Required params
---------------

Required params for action **upload\_image** :

    'image' - a binary file (using Content-type: multipart/form-data)

Required params for action **insert\_ad** :

    'category' - the id of cars category (2020)
    'type' - type of ad (s=sell)
    'subject' - title of ad
    'name' - name of advertiser
    'region' - region number of chile
    'email' - email of advertiser
    'body' - body of the ad
    'price' - price of the car
    'phone' - phone to contact the advertiser
    'regdate' - car year
    'brand' - brand of the car
    'model'- model of the car
    'cartype' - car type
    'version' - car version
    'mileage' - the kilometers of the car
    'gearbox' - gear type of the car
    'fuel' - fuel that the car uses
    'import' - Must always be 1
    'external_ad_id' - Your ad identifier
    'plates' - identification unique for cars (in Chile is called patente)

Optional params for action **insert\_ad** :

    'image_id(0-N)' - image_id returned by action 'upload_image', one for each uploaded image.

Response for actions

**Upload\_image**

-   error response example:

        { "newad": { "status": "IMAGE_ERROR", "message": "ERROR_IMAGE_TYPE"} }

-   success response example:

        { "newad": { "image_id": "1756995700.jpg", "status": "OK"} }

**Insert\_ad**

-   error response example:

        { "newad": {
            "status": "TRANS_ERROR",
            "subject": "ERROR_SUBJECT_MISSING",
            "subject_error_label": "Escribe un tÃ­tulo"
            }
        }

-   success response example:

        { "newad": {
          "status": "TRANS_OK",
          }
        }

Data types and values for all params
------------------------------------

    subject - string [maxlength = 50]
    name - string    [maxlength = 50]
    region - integer between 1 and 15 => [15 = 'RegiÃ³n Metropolitana', 1 = 'XV Arica & Parinacota', 2 = 'I TarapacÃ¡', 3 = 'II Antofagasta', 4 = 'III Atacama', 5 = 'IV Coquimbo', 6 = 'V ValparaÃ­so', 7 = 'VI O'Higgins', 8 = 'VII Maule', 9 = 'VIII BiobÃ­o', 10 = 'IX AraucanÃ­a', 11 = 'XIV Los RÃ­os', 12 = 'X Los Lagos', 13 = 'XI AisÃ©n', 14 = 'XII Magallanes & AntÃ¡rtica']
    email - string   [maxlength = 60]
    category - integer = 2020
    type - string [s]
    body - string   [maxlength = 2000]
    price - integer  value  between  0  and 2000000000
    image_id0..N - string return by upload_image action
    phone - string  [minlength = 6 , maxlength = 50]
    cartype - integer between 1 and 5 => [1='Automovil', 2='Camioneta', 3='4x4', 4='Convertible', 5='Clasico']
    brand - integer obtained in **Cars Data**
    model - integer obtained in **Cars Data**
    version - integer obtained in **Cars Data**
    regdate - integer between 1960 and 2014 (if your car year is less than 1960, use 1900)
    mileage - integer between 0 and 999999
    gearbox - integer 1 or 2 => [1='Manual', 2='Automatico']
    fuel - integer between 1 and 5 => [1='Bencina', 2='Hibrido', 3='Gas', 4='Diesel', 5='Otros']
    import - integer = 1
    external_ad_id - string regex  ^[A-Za-z0-9_{}-]+   [minlength = 1 ,  maxlentgh = 50]
    plates - string regex  ^[A-Za-z]{2}([A-Za-z]{2}|[0-9]{2})[0-9]{2}$  [minlength = 5 ('motorcycles') ,  maxlentgh = 6 ('cars', 'trucks')]

ImportDeletead
==============

-   **URL** - <http://m.yapo.cl/api/importdeletead.json>
-   **Allowed request methods** - \[POST\]

Used for deleting a specific import ad.

Required params:

    'external_ad_id' - external_ad_id of the ad you want to delete

Error response example:

    { "error": "ERROR_AD_ALREADY_DELETED" ,"status": "TRANS_ERROR" }

Success response example:

    { "status": "TRANS_OK:ok, ad 9000000 deleted" }

Cars Data
=========

-   **URL** - <http://m.yapo.cl/api/cars_data.json>
-   **Allowed request methods** - \[POST\]

Used for get brands or model of especific brand

Optional params:

    'br' - brand of the car

if there is **no brand**, it returns the brand list

-   Success response example:

        {
             "brands":
                [{
                    "id": "1",
                    "value": "Acadian"
                }, {
                    "id": "2",
                    "value": "Acura"
                },
                .
                .
                .
                {
                    "id": "90",
                    "value": "Volvo"
                }, {
                    "id": "91",
                    "value": "Willys"
                }, {
                    "id": "92",
                    "value": "Yugo"
                }, {
                    "id": "93",
                    "value": "Zastava"
                }, {
                    "id": "94",
                    "value": "Zotye"
                }, {
                    "id": "95",
                    "value": "Zx"
            }]
        }

if **brand exists**, it returns the model and versions, you should only
use the **keys** values

-   Success response example br=90 (volvo):

        {
        "models": [{
            "key": "1",
            "name": "142",
            "versions": [{
                "key": "1",
                "name": "142",
                "cartype": "1",
                "attributes": [{
                    "year": "1971",
                    "fuel": "1",
                    "gearbox": "1",
                    "appraisal": "200000",
                    "ndoors": "2",
                    "cyl": "1990",
                    "equip": "NORM",
                    "circulation": "20003",
                    "code": "A580170"
                }, {
                    "year": "1972",
                    "fuel": "1",
                    "gearbox": "1",
                    "appraisal": "200000",
                    "ndoors": "2",
                    "cyl": "1990",
                    "equip": "NORM",
                    "circulation": "20003",
                    "code": "A580170"
                }, {
                    "year": "1973",
                    "fuel": "1",
                    "gearbox": "1",
                    "appraisal": "210000",
                    "ndoors": "2",
                    "cyl": "1990",
                    "equip": "NORM",
                    "circulation": "20003",
                    "code": "A580170"
                }, {
                    "year": "1974",
                            "fuel": "1",
                    "gearbox": "1",
                    "appraisal": "250000",
                    "ndoors": "2",
                    "cyl": "1990",
                    "equip": "NORM",
                    "circulation": "20003",
                    "code": "A580170"
                }]
            }]
        }
            .
            .
            .
        ]
        }
