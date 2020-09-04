# Members Field Type

`members` is a custom field type for selecting members from the official Perch Members app.

## Installation

Download and place the `members` folder in `perch/addons/fieldtypes`.

## Requirements

- Perch or Perch Runway 3.0+
- Perch Members app

## Example usage

```html
<perch:content id="member" type="members" label="Member" max="1" required>
```

```php
if(perch_member_logged_in()) {
    perch_collection('Tickets', [
        'template'  => 'tickets/_list',
        'filter'    => 'member',
        'value'     => perch_member_get('id'),
    ]);
}
```

### Controlling the number of members in the dropdown

By default the field only loads the last 10 members (plus any previously selected members). This is to not aimlessly fetch all members (which can be 100s) every time you visit an edit form that contains this field. You can search members by their email address.

```html
<perch:content id="members" type="members" label="Members" required>
```

If you know the site only has a small number of registered members and wish to load them all, you can use the all attribute:

```html
<perch:content id="members" type="members" label="Members" all required>
```