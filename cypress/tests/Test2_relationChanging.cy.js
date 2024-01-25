import '../support/commands.js';

describe('Title Page for Preprint Plugin - Title page update on relation changing', function() {
    let submissionData;
    
    before(function() {
        Cypress.config('defaultCommandTimeout', 4000);
        submissionData = {
            title: "A Nightmare on Elm Street",
            relations: 3,
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

        cy.get('#publication-button').click();
		cy.get('.pkpHeader__actions button:contains("Unpost")').click();
        cy.get('.modal__panel button:contains("Unpost")').click();
        
        cy.setTinyMceContent('titleAbstract-title-control-en', submissionData.title);
        cy.get('#titleAbstract button:contains("Save")').click();
        cy.waitJQuery();

        cy.get('.pkpHeader__actions button:contains("Post")').click();
        cy.get('.pkp_modal_panel button:contains("Post")').click();
        cy.contains('span', 'Posted');

        cy.contains('button', 'Relations').click();
        cy.get('input[name="relationStatus"][value="3"]').check();
        cy.get('.pkpWorkflow__relateForm button:contains("Save")').click();
        cy.waitJQuery();

        cy.contains('a', 'View').click();
        cy.contains('a', 'PDF').click();
        cy.get('a.download').invoke('attr', 'href').then(pdfUrl => {
            cy.performTitlePageCheckings(submissionData, pdfUrl);
        });
    });
})

