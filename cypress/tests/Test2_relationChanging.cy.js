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
        cy.findSubmissionAsEditor('dbarnes', null, 'Ostrom');
        cy.get('#publication-button').click();

        cy.contains('button', 'Relations').click();
        cy.get('input[name="relationStatus"][value="3"]').check();
        cy.get('input[name="vorDoi"]').type(submissionData.vorDoi, {delay: 0});
        cy.get('.pkpWorkflow__relateForm button:contains("Save")').click();
        cy.waitJQuery();

        cy.contains('a', 'View').click();
        cy.contains('a', 'PDF').click();
        cy.get('a.download').invoke('attr', 'href').then(pdfUrl => {
            cy.performTitlePageCheckings(submissionData, pdfUrl);
        });
    });
})

