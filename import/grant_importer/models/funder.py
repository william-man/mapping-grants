class Funder:
    def __init__(self, funderOrganisation):
        self.id = funderOrganisation['id']
        self.name = funderOrganisation['name']

    def to_dict(self):
        return {
            'organisationID': self.id,
            'name': self.name
        }
