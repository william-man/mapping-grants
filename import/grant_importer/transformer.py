import ijson

from grant_importer.models.funder import Funder
from grant_importer.models.recipient_org import RecipientOrg
from grant_importer.models.recipient_individual import RecipientIndividual
from grant_importer.models.grant import Grant

class Transformer:
    def process_file(self, filepath):
        with open(filepath, 'r') as file:
            objects = ijson.items(file, "grants.item")
            funders = {}
            recipient_orgs = {}
            recipient_individuals = {}
            grants = []

            for grant in objects:
                additional_data = grant.get('additional_data', {})
                funder = Funder(grant['fundingOrganization'][0])
                recipient_org = None
                recipient_individual = None

                if 'recipientOrganization' in grant:
                    recipient_org = RecipientOrg(grant.get('recipientOrganization')[0], additional_data)
                
                if 'recipientIndividual' in grant:
                    recipient_individual = RecipientIndividual(grant.get('recipientIndividual'), additional_data)

                if recipient_org is None and recipient_individual is None:
                    continue
                
                grant_item = Grant(grant, funder, recipient_org, recipient_individual)

                funders[funder.id] = funder.to_dict()
                if recipient_org is not None:
                    recipient_orgs[recipient_org.id] = recipient_org.to_dict()
                if recipient_individual is not None:
                    recipient_individuals[recipient_individual.id] = recipient_individual.to_dict()
                grants.append(grant_item.to_dict())
        return {
            'funding_organisations': funders,
            'recipient_organisations': recipient_orgs,
            'recipient_individuals': recipient_individuals,
            'grants': grants
        }