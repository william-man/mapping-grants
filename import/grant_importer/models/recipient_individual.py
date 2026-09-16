class RecipientIndividual:
    REGION_COORDINATES = {
        "north east": (54.9783, -1.6178),
        "north west": (53.4808, -2.2426),
        "yorkshire and the humber": (53.9591, -1.0815),
        "east midlands": (52.9548, -1.1581),
        "west midlands": (52.4862, -1.8904),
        "east of england": (52.2405, 0.9027),
        "london": (51.5074, -0.1278),
        "south east": (51.2362, -0.5704),
        "south west": (50.9097, -3.5900),
        "scotland": (56.4907, -4.2026),
        "wales": (52.1307, -3.7837),
        "northern ireland": (54.7877, -6.4923),
    }

    def __init__(self, recipientIndividual, additionalData):
        self.id = recipientIndividual.get('id', None)
        self.name = recipientIndividual.get('name', None)
        self.data = additionalData

    def to_dict(self):
        individualInfo = {
            'individualID': self.id,
            'name': self.name,
        }

        individualInfo.update(self.get_location())
        return individualInfo

    def get_location(self):
        regionName = self.data.get('recipientRegionName', '')
        coords = REGION_COORDINATES.get(regionName.lower(), (None, None))

        return {
            'latitude': coords[0],
            'longitude': coords[1],
            'regionName': regionName,
        }
        