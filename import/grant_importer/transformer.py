import ijson
import datetime
import json
import csv

from decimal import Decimal

class Transformer:

    def __init__(self):
        self.fields = [
            'grantsID',
            'title',
            'description',
            'currency',
            'amountAwarded',
            'awardDate',
            'fundingOrganization', 
            'recipientOrganization', 
            'recipientIndividual',
            'recipientPostcode'
        ]
        
    def process_file(self, filepath):
        json = open(filepath, "r")
        objects = ijson.items(json, "grants.item")
        
        rows = []
        for grant in objects:
            row = self.extract_fields(grant)
            rows.append(row)

        self.write_csv("output.csv", rows)
        json.close()

    def extract_fields(self, grant):
        extracted = {field: grant.get(field) for field in self.fields}
        extracted['grantsID'] = grant.get('id')
        extracted['awardDate'] = self.parse_award_date(grant.get('awardDate'))
        extracted['recipientPostcode'] = self.get_recipient_postcode(grant)

        for field in ['fundingOrganization', 'recipientOrganization', 'recipientIndividual']:
            extracted[field] = json.dumps(extracted[field], default=self.json_default)
        return extracted

    def parse_award_date(self, date_str):
        if not date_str:
            return None
        try:
            return datetime.datetime.strptime(date_str[:10], "%Y-%m-%d").date()
        except ValueError:
            return None

    def get_recipient_postcode(self, grant):
        infos = grant.get('additional_data', {}).get('recipientOrgInfos', [])
        for info in infos:
            postcode = info.get('postalCode')
            if postcode:
                return postcode
        return None

    def json_default(self, object): 
        if isinstance(object, Decimal):
            return float(object)
        raise TypeError(f"Object of type {object.__class__.__name__} is not JSON serializable")

    def write_csv(self, output_file, rows):
        with open(output_file, "w", newline="") as csvfile:
            write = csv.DictWriter(csvfile, fieldnames=self.fields, restval='', extrasaction='ignore')
            write.writeheader()
            write.writerows(rows)