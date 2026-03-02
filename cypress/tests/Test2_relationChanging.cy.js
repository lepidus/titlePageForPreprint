import '../support/commands.js';

describe('Title Page for Preprint Plugin - Title page update on relation changing', function() {
    let submissionData;
    
    before(function() {
        Cypress.config('defaultCommandTimeout', 4000);
        submissionData = {
            title: "A new nightmare",
            relations: 3,
            vorDoi: 'https://doi.org/10.1234/nonexistentDoi',
            contributors: [
                {
                    'given': 'Wes',
                    'family': 'Craven',
                    'email': 'wes.craven@stab.com',
                    'country': 'United States'
                }
            ]
        };
    });
    
    it('Moderator changes submission relations after it has been posted', function() {
        cy.login('dbarnes', null, 'publicknowledge');
        cy.openSubmission('Published', submissionData.title);

        cy.contains('button', 'Relations').click();
        cy.get('input[name="relationStatus"][value="3"]').check();
        cy.get('input[name="vorDoi"]').type(submissionData.vorDoi, {delay: 0});
        cy.get('.pkpWorkflow__publicationRelation button:contains("Save")').click();
        cy.wait(1000);

        cy.get('.DialogContent:visible').within(() => {
            cy.contains('button', 'View').click();
        });
        cy.contains('a', 'PDF').click();
        cy.get('a.download').invoke('attr', 'href').then(pdfUrl => {
            cy.performTitlePageCheckings(submissionData, pdfUrl);
        });
    });
})

