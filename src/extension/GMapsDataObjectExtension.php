<?php
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;

class GMapsDataObjectExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
    'Address'  =>  'Text',
    'Longitude' =>  'Text',
    'Latitude'  =>  'Text'
  ];
    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        $fields->addFieldsToTab(
      'Root.Maps',
      [
        TextField::create(
          'Address',
          'Adresse ("ß" als "ss" schreiben)'
        ),
        LiteralField::create(
          'Notice',
          'Longitude und Latitude wird mit angegebener Adresse automatisch befüllt'
        ),
        TextField::create(
          'Longitude',
          'Longitude'
        ),
        TextField::create(
          'Latitude',
          'Latitude'
        )
      ]
    );
        return $fields;
    }
    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (array_key_exists("Address", $this->owner->getChangedFields(false, 1))) {
            if ($this->owner->getChangedFields(true, 1)["Address"]["after"] != "") {
                if ($this->owner->Address != "") {
                    $geo = new simpleGMapGeocoder();
                    $result = $geo->getGeoCoords($this->owner->Address);
                    $this->owner->Longitude = $result["lng"];
                    $this->owner->Latitude = $result["lat"];
                }
            }
        }
    }
}
