# Tax Rule Import

This module add import and export for tax rules

/!\ Tax rule export filter will be available in Thelia 2.1.4 and 2.2.0-alpha2

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is TaxRuleImport.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/tax-rule-import-module:~1.0
```

## Usage

### With Thelia

First, create your tax rule with Thelia.

Then, to the export (or import) page and use the "Tax rule" export (or import)

### Manually

If you want to create your tax rule file, you'll have to write an XML file.

The root tag is called ```tax-rules```, then each tax rule go into a ```tax-rule```.

Example:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<tax-rules>
    <tax-rule>
        ...
    </tax-rule>
    <tax-rule>
        ...
    </tax-rule>
    <!-- You can add as many tax rules as you want -->
</tax-rules>
```

Then, each tax rule must have two tags:
- countries
- descriptives

It can have a ```taxes``` tag too.

#### The <countries> tag

This tag must contain at least one ```country``` tag. This is the countries where the tax rules has to be applied.

The value of country can be the isoalpha2, isoalpha3 or isocode of the country.

Example:

```xml
...
<countries>
    <country>FRA</country>
    <country>USA</country>
</countries>
...
```

#### The <descriptives> tag

This tag must contain at least one ```descriptive``` tag, that has a mandatory attribute ```locale```.
Then, the ```descriptive``` tag can have two children:
- <title> The tax rule title
- <description> The tax rule description
 
Example:
```xml
<descriptives>
  <descriptive locale="en_US">
    <title>French 20% VAT</title>
  </descriptive>
  <descriptive locale="fr_FR">
    <title>TVA française à 20%</title>
  </descriptive>
</descriptives>
```

#### The <taxes> tag

This tag contains at least one ```tax``` tag, that has a mandatory attribute ```type```.

The type attribute can be:
- percent : this is a shortcut for ```Thelia\TaxEngine\TaxType\PricePercentTaxType```
- amount : this is a shortcut for ```Thelia\TaxEngine\TaxType\FixAmountTaxType```
- feature_percent : this is a shortcut for ```Thelia\TaxEngine\TaxType\FeatureFixAmountTaxType```
- Your own tax class

The ```tax``` tag can have two children:
- <descriptives> This tag works exactly like ```tax-rule```'s one
- <requirement> This tag has a mandatory attribute ```key``` that is the class's requirement name, and the value is the requirement's. You may add as many requirement tag as you want.

### Example

```xml
<?xml version="1.0" encoding="UTF-8"?>
<tax-rules>
  <tax-rule>
    <countries>
      <country>FRA</country>
    </countries>
    <descriptives>
      <descriptive locale="en_US">
        <title>French 20% VAT</title>
      </descriptive>
      <descriptive locale="es_ES">
        <title/>
      </descriptive>
      <descriptive locale="fr_FR">
        <title>TVA française à 20%</title>
      </descriptive>
    </descriptives>
    <taxes>
      <tax type="percent">
        <descriptives>
          <descriptive locale="en_US">
            <title>French 20% VAT</title>
          </descriptive>
          <descriptive locale="es_ES">
            <title/>
          </descriptive>
          <descriptive locale="fr_FR">
            <title>TVA française à 20%</title>
          </descriptive>
        </descriptives>
        <requirement key="percent">20</requirement>
      </tax>
    </taxes>
  </tax-rule>
  <tax-rule>
    <countries>
      <country>FRA</country>
    </countries>
    <descriptives>
      <descriptive locale="en_US">
        <title>French 10% VAT</title>
      </descriptive>
      <descriptive locale="es_ES">
        <title/>
      </descriptive>
      <descriptive locale="fr_FR">
        <title>TVA française à 10%</title>
      </descriptive>
    </descriptives>
    <taxes>
      <tax type="percent">
        <descriptives>
          <descriptive locale="en_US">
            <title>French 10% VAT</title>
          </descriptive>
          <descriptive locale="es_ES">
            <title/>
          </descriptive>
          <descriptive locale="fr_FR">
            <title>TVA française à 10%</title>
          </descriptive>
        </descriptives>
        <requirement key="percent">10</requirement>
      </tax>
    </taxes>
  </tax-rule>
</tax-rules>
```