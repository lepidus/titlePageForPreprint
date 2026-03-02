import '../support/commands.js';

describe('Title Page for Preprint Plugin - Title page stamping on preprint posting', function() {
    let submissionData;
    
    before(function() {
        Cypress.config('defaultCommandTimeout', 10000);
        submissionData = {
            title: "A Nightmare on Elm Street",
			abstract: 'Teenagers start to dream with a creepy man',
			keywords: ['plugin', 'testing'],
            relations: 1,
            contributors: [
                {
                    'given': 'Wes',
                    'family': 'Craven',
                    'email': 'wes.craven@stab.com',
                    'country': 'United States'
                }
            ],
            files: [
                {
                    'file': 'dummy.pdf',
                    'fileName': 'dummy.pdf',
                    'mimeType': 'application/pdf',
                    'genre': 'Preprint Text'
                }
            ]
		};
    });

    it('Author creates new submission with galley', function() {
        cy.login('eostrom', null, 'publicknowledge');
        cy.contains('Start A New Submission').click();

        cy.beginSubmission(submissionData);
        cy.detailsStep(submissionData);
        cy.filesStep(submissionData);
        cy.contributorsStep(submissionData);
        cy.get('input[name="relationStatus"][value="1"]').check();
        cy.advanceNSubmissionSteps(1);
        cy.contains('button', 'Submit').click();
        cy.get('.DialogContent:visible').within(() => {
            cy.contains('button', 'Submit').click();
        });

        cy.waitJQuery();
        cy.contains('h1', 'Submission complete');
    });
    it('Moderator posts submission. Title page is stamped on PDF', function () {
        cy.login('dbarnes', null, 'publicknowledge');
        cy.openSubmission('Active submissions', submissionData.title);
        
        cy.contains('button', 'Post the preprint').click();
        cy.contains('button', 'Post').click();
        cy.contains('All requirements have been met');
        cy.get('button:visible:contains("Post")').click();
        cy.wait(1000);
        cy.contains('span', 'Published');
        
        cy.get('.DialogContent:visible').within(() => {
            cy.contains('button', 'View').click();
        });
        cy.contains('a', 'PDF').click();
        cy.get('a.download').invoke('attr', 'href').then(pdfUrl => {
            cy.performTitlePageCheckings(submissionData, pdfUrl);
        });
    });
    it('Title page updating', function () {
        cy.login('dbarnes', null, 'publicknowledge');
        cy.openSubmission('Published', submissionData.title);
        
        cy.contains('button', 'Unpost').click();
        cy.get('.DialogContent:visible').within(() => {
            cy.contains('button', 'Unpost').click();
        });
        cy.wait(1000);
        
        cy.openWorkflowMenu('Title & Abstract');
        submissionData.title = 'A new nightmare';
        cy.setTinyMceContent('titleAbstract-title-control-en', submissionData.title);
        cy.get('button').contains('Save').click();
		cy.get('[role="status"]').contains('Saved');

        cy.contains('button', 'Post').click();
        cy.contains('All requirements have been met');
        cy.get('button:visible:contains("Post")').click();
        cy.wait(1000);
        cy.contains('span', 'Published');

        cy.get('.DialogContent:visible').within(() => {
            cy.contains('button', 'View').click();
        });
        cy.contains('a', 'PDF').click();
        cy.get('a.download').invoke('attr', 'href').then(pdfUrl => {
            cy.performTitlePageCheckings(submissionData, pdfUrl);
        });
    });
});