# CiviCRM: Extended Quick Search

Extends Quick Search to include a configurable collection of custom fields.

## Limitations
Only custom fields attached to contacts are supported.

## Configuration
A configuration GUI is planned, but for the time being, configuration is achieved
by adding lines like these to civicrm.settings.php:

```php
global $civicrm_setting;
$civicrm_setting['com.joineryhq.extquicksearch']['search_config'] = array(
  N => array(
    CRITERION, CRITERION, CRITERION
  ),
);
```

* N: An integer indicating the sytem ID of a custom field
* CRITERION: Any one or more of the following Quick Search criteria, which have
the meaning shown here:
  * 'default': Name/Email
  * 'contact_id': Contact ID
  * 'external_identifier': External ID
  * 'first_name': First Name
  * 'last_name': Last Name
  * 'email': Email
  * 'phone_numeric': Phone
  * 'street_address': Street Address
  * 'city': City
  * 'postal_code': Postal Code
  * 'job_title': Job Title

With this configuration, the custom field N will be included when searching the
specified Quick Search criterion.

### Example
An example CiviCRM installation has a custom field labeled "Superhero Codename",
which is custom field ID 40.

With this extension disabled, using the Quick Search box to search for "man" gets
us these results:
```
Holberman, Jane :: janeh@example.org
Smith, Herman :: hermans@example.com
```

We then enable the extension, and specify the following configuration:
```php
global $civicrm_setting;
$civicrm_setting['com.joineryhq.extquicksearch']['search_config'] = array(
  40 => array(
    'default'
  ),
);
```

Using the Quick Search box to search for "man" now gets us these results:
```
Kent, Clark :: super@superheros.example.com
Holberman, Jane :: janeh@example.org
Smith, Herman :: hermans@example.com
Wayne, Bruce :: bat@superheros.example.com
```

## Support
![screenshot](/images/joinery-logo.png)

Joinery provides services for CiviCRM including custom extension development, training, data migrations, and more. We aim to keep this extension in good working order, and will do our best to respond appropriately to issues reported on its [github issue queue](https://github.com/twomice/com.joineryhq.extquicksearch/issues). In addition, if you require urgent or highly customized improvements to this extension, we may suggest conducting a fee-based project under our standard commercial terms.  In any case, the place to start is the [github issue queue](https://github.com/twomice/com.joineryhq.extquicksearch/issues) -- let us hear what you need and we'll be glad to help however we can.

And, if you need help with any other aspect of CiviCRM -- from hosting to custom development to strategic consultation and more -- please contact us directly via https://joineryhq.com