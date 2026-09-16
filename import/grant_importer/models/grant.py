import datetime

class Grant:
    def __init__(self, grant, funder, recipientOrg, recipientIndividual):
        self.id = grant.get('id')
        self.title = grant.get('title')
        self.description = grant.get('description')
        self.currency = grant.get('currency')
        self.amountAwarded = grant.get('amountAwarded')
        self.awardDate = self.parse_award_date(grant.get('awardDate'))
        self.fundingOrganisation = funder.id
        self.recipientOrganisation = recipientOrg.id if recipientOrg is not None else None
        self.recipientIndividual = recipientIndividual.id if recipientIndividual is not None else None

    def to_dict(self):
        return {
            'grantsID': self.id,
            'title': self.title,
            'description': self.description,
            'currency': self.currency,
            'amountAwarded': self.amountAwarded,
            'awardDate': self.awardDate,
            'fundingOrganisation': self.fundingOrganisation,
            'recipientOrganisation': self.recipientOrganisation,
            'recipientIndividual': self.recipientIndividual
        }

    def parse_award_date(self, date_str):
        if not date_str:
            return None
        try:
            return datetime.datetime.strptime(date_str[:10], "%Y-%m-%d").date()
        except ValueError:
            return None
