# CiviCRM: Extended Quick Search

1. QuickSearch matching: Extends Quick Search to match on a configurable collection of custom fields.
2. Current Employer display: Provides options for adding Current Employer as a visible field on results in Quick Search and Contact Reference fields.

## Usage
1. QuickSearch matching: see [QuickSearch matching](#quicksearch-matching) below for important notes on limitations and configuration.
2. Current Employer display: Navigate to _Administer > Customize Data and Screens > Search Preferences_, and there find the settings "Autocomplete Contact Search" (for Quick Search) and "Contact Reference Options" (for contact reference fields). In either or both of these settings, enable the "Current Employer" option. Save settings. Current Employer will now be included in user-visible results on the relevant features.

## QuickSearch matching

### Limitations
Only custom fields attached to contacts are supported.

### Configuration
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

* N: An integer indicating the system ID of a custom field
* CRITERION: Any one or more of the following Quick Search criteria, which have
the meaning shown here:
  * 'sort_name': Name/Email
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

#### Example
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
    'sort_name'
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
