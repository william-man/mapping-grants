class RecipientOrg:
    def __init__(self, recipientOrg, additionalData):
        self.id = recipientOrg['id']
        self.name = recipientOrg['name']
        self.postcode = recipientOrg.get('postalCode', None)
        self.data = additionalData.get('locationLookup', [])

    def to_dict(self):
        coords = self.get_coordinates()
        return {
            'organisationID': self.id,
            'name': self.name,
            'postalCode': self.postcode,
            'latitude': coords.get('latitude'),
            'longitude': coords.get('longitude')
        }

    def get_coordinates(self):
        if len(self.data) == 0:
            return {'latitude': None, 'longitude': None}

        coords = {
            'latitude': None,
            'longitude': None
        }
        for item in self.data:
            if item.get('source') == 'recipientOrganizationPostcode':
                coords.update({
                    'latitude': item.get('latitude'),
                    'longitude': item.get('longitude')
                })
        
        return coords
            
