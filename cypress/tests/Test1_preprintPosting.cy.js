function beginSubmission(submissionData) {
    cy.get('input[name="locale"][value="en"]').click();
    cy.setTinyMceContent('startSubmission-title-control', submissionData.title);
    cy.get('input[name="submissionRequirements"]').check();
    cy.get('input[name="privacyConsent"]').check();

    cy.contains('button', 'Begin Submission').click();
}

function detailsStep(submissionData) {
    cy.setTinyMceContent('titleAbstract-abstract-control-en', submissionData.abstract);
    submissionData.keywords.forEach(keyword => {
        cy.get('#titleAbstract-keywords-control-en').type(keyword, {delay: 0});
        cy.get('#titleAbstract-keywords-control-en').type('{enter}', {delay: 0});
    });
    
    cy.contains('button', 'Continue').click();
}

function filesStep(submissionData) {
    cy.addSubmissionGalleys(submissionData.files);
    cy.contains('button', 'Continue').click();
}

function contributorsStep(submissionData) {
    submissionData.contributors.forEach(authorData => {
        cy.contains('button', 'Add Contributor').click();
        cy.get('input[name="givenName-en"]').type(authorData.given, {delay: 0});
        cy.get('input[name="familyName-en"]').type(authorData.family, {delay: 0});
        cy.get('input[name="email"]').type(authorData.email, {delay: 0});
        cy.get('select[name="country"]').select(authorData.country);
        
        cy.get('.modal__panel:contains("Add Contributor")').find('button').contains('Save').click();
        cy.waitJQuery();
    });

    cy.contains('button', 'Continue').click();
}

describe('Title Page for Preprint Plugin - Title page stamping on preprint posting', function() {
    let submissionData;
    
    before(function() {
        Cypress.config('defaultCommandTimeout', 4000);
        submissionData = {
            title: "A Nightmare on Elm Street",
			abstract: 'Teenagers start to dream with a creepy man',
			keywords: ['plugin', 'testing'],
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
        cy.get('div#myQueue a:contains("New Submission")').click();

        beginSubmission(submissionData);
        detailsStep(submissionData);
        filesStep(submissionData);
        contributorsStep(submissionData);
        cy.get('input[name="relationStatus"][value="1"]').check();
        cy.contains('button', 'Continue').click();
        cy.contains('button', 'Submit').click();
        cy.get('.modal__panel:visible').within(() => {
            cy.contains('button', 'Submit').click();
        });

        cy.waitJQuery();
        cy.contains('h1', 'Submission complete');
    });
    it('Moderator posts submission. Title page is stamped on PDF', function () {
        cy.findSubmissionAsEditor('dbarnes', null, 'Ostrom');
        cy.get('#publication-button').click();
		cy.get('.pkpHeader .pkpHeader__actions button:contains("Post")').click();
        cy.get('.pkp_modal_panel button:contains("Post")').click();
        cy.contains('span', 'Posted');
        
        //verificar que adicionou folha de rosto e página da checklist
    });
});

//retirar o preprint
//alterar alguma info nos metadados da submissão
//repostar
//verificar que alterou a folha, mas que ainda tem só uma