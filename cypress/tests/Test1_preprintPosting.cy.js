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

function downloadPdfFile(pdfUrl, directory, pdfFile, txtFile) {
    cy.exec('mkdir ' + directory).its('code').should('eq', 0);
    cy.exec('curl -o ' + pdfFile + ' ' + pdfUrl).its('code').should('eq', 0);
    cy.exec('pdftotext ' + pdfFile + ' ' + txtFile).its('code').should('eq', 0);
}

function assertNumberPdfPages(pdfFile, expectedNumberOfPages) {
    cy.exec('cpdf -pages ' + pdfFile).its('stdout').should('contain', expectedNumberOfPages);
}

function checkTitlePage(txtFile, submissionData) {
    cy.exec("grep '" + submissionData.title + "' " + txtFile).its('code').should('eq', 0);
    cy.exec("grep 'Publication status: This preprint has not been published elsewhere' " + txtFile).its('code').should('eq', 0);
    
    let authorFullName = submissionData.contributors[0]['given'] + ' '  + submissionData.contributors[0]['family'];
    cy.exec("grep '" + authorFullName + "' " + txtFile).its('code').should('eq', 0);
    
    let today = (new Date()).toISOString().split('T')[0];
    cy.exec("grep 'Submitted on: " + today + "' " + txtFile).its('code').should('eq', 0);
    cy.exec("grep 'Posted on: " + today + "' " + txtFile).its('code').should('eq', 0);

    cy.exec("grep 'This document is a preprint and its current status is available at:' " + txtFile).its('code').should('eq', 0);
}

function checkChecklistPage(txtFile) {
    cy.exec("grep 'This preprint was submitted under the following conditions:' " + txtFile).its('code').should('eq', 0);
    
    cy.exec("grep 'This submission meets the requirements outlined in the Author Guidelines.' " + txtFile).its('code').should('eq', 0);
    cy.exec("grep 'This submission has not been previously posted.' " + txtFile).its('code').should('eq', 0);
    cy.exec("grep 'All references have been checked for accuracy and completeness.' " + txtFile).its('code').should('eq', 0);
    cy.exec("grep 'All tables and figures have been numbered and labeled.' " + txtFile).its('code').should('eq', 0);
}

function performTitlePageCheckings(pdfUrl, submissionData) {
    const directory = './plugins/generic/titlePageForPreprint/cypress/tests/result/';
    const pdfFile = directory + 'document.pdf';
    const txtFile = directory + 'document.txt';
    const expectedNumberOfPages = 3;
    
    downloadPdfFile(pdfUrl, directory, pdfFile, txtFile);

    assertNumberPdfPages(pdfFile, expectedNumberOfPages);
    checkTitlePage(txtFile, submissionData);
    checkChecklistPage(txtFile);

    cy.exec('rm -r ' + directory).its('code').should('eq', 0);
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

    /*it('Author creates new submission with galley', function() {
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
		cy.get('.pkpHeader__actions button:contains("Post")').click();
        cy.get('.pkp_modal_panel button:contains("Post")').click();
        cy.contains('span', 'Posted');
        
        cy.contains('a', 'View').click();
        cy.contains('a', 'PDF').click();
        cy.get('a.download').invoke('attr', 'href').then(pdfUrl => {
            performTitlePageCheckings(pdfUrl, submissionData);
        });
    });*/
    it('Title page updating', function () {
        cy.findSubmissionAsEditor('dbarnes', null, 'Ostrom');
        cy.get('#publication-button').click();
		cy.get('.pkpHeader__actions button:contains("Unpost")').click();
        cy.get('.modal__panel button:contains("Unpost")').click();
        
        submissionData.title = 'A new nightmare';
        cy.setTinyMceContent('titleAbstract-title-control-en', submissionData.title);
        cy.get('#titleAbstract button:contains("Save")').click();
        cy.waitJQuery();

        cy.get('.pkpHeader__actions button:contains("Post")').click();
        cy.get('.pkp_modal_panel button:contains("Post")').click();
        cy.contains('span', 'Posted');

        cy.contains('a', 'View').click();
        cy.contains('a', 'PDF').click();
        cy.get('a.download').invoke('attr', 'href').then(pdfUrl => {
            performTitlePageCheckings(pdfUrl, submissionData);
        });
    });
});