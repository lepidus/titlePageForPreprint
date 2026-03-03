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
    
    if(submissionData.relations == 1) {
        cy.exec("grep 'Publication status: This preprint has not been published elsewhere.' " + txtFile).its('code').should('eq', 0);
    } else if (submissionData.relations == 3) {
        cy.exec("grep 'Publication status: This preprint has been published elsewhere.' " + txtFile).its('code').should('eq', 0);

        if('vorDoi' in submissionData) {
            cy.exec("grep 'DOI of the published preprint: " + submissionData.vorDoi + "' " + txtFile).its('code').should('eq', 0);
        }
    }
    
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

Cypress.Commands.add('performTitlePageCheckings', function (submissionData, pdfUrl) {
    const directory = './plugins/generic/titlePageForPreprint/cypress/tests/result/';
    const pdfFile = directory + 'document.pdf';
    const txtFile = directory + 'document.txt';
    const expectedNumberOfPages = 3;
    
    downloadPdfFile(pdfUrl, directory, pdfFile, txtFile);

    assertNumberPdfPages(pdfFile, expectedNumberOfPages);
    checkTitlePage(txtFile, submissionData);
    checkChecklistPage(txtFile);

    cy.exec('rm -r ' + directory).its('code').should('eq', 0);
});

Cypress.Commands.add('advanceNSubmissionSteps', function (numberOfSteps) {
    for (let stepsAdvanced = 0; stepsAdvanced < numberOfSteps; stepsAdvanced++) {
        cy.get('.submissionWizard__footer').within(() => {
            cy.contains('button', 'Continue').click();
        });
        cy.wait(200);
    }
});

Cypress.Commands.add('beginSubmission', function(submissionData) {
    cy.get('label:contains("English")').click();
    cy.setTinyMceContent('startSubmission-title-control', submissionData.title);

    cy.get('input[name="submissionRequirements"]').check();
    cy.get('input[name="privacyConsent"]').check();
    cy.contains('button', 'Begin Submission').click();
});

Cypress.Commands.add('detailsStep', function(submissionData) {
    cy.setTinyMceContent('titleAbstract-abstract-control-en', submissionData.abstract);
    submissionData.keywords.forEach(keyword => {
        cy.get('#titleAbstract-keywords-control-en').type(keyword, {delay: 0});
        cy.get('#titleAbstract-keywords-control-en').type('{enter}', {delay: 0});
    });

    cy.advanceNSubmissionSteps(1);
});

Cypress.Commands.add('filesStep', function(submissionData) {
    cy.addSubmissionGalleys(submissionData.files);
    cy.advanceNSubmissionSteps(1);
});

Cypress.Commands.add('contributorsStep', function(submissionData) {
    submissionData.contributors.forEach(authorData => {
        cy.contains('button', 'Add Contributor').click();
        cy.get('input[name="givenName-en"]').type(authorData.given, {delay: 0});
        cy.get('input[name="familyName-en"]').type(authorData.family, {delay: 0});
        cy.get('input[name="email"]').type(authorData.email, {delay: 0});
        cy.get('select[name="country"]').select(authorData.country);
        
        cy.get('div[role=dialog]:contains("Add Contributor")').find('button').contains('Save').click();
        cy.wait(1000);
    });

    cy.advanceNSubmissionSteps(1);
});

Cypress.Commands.add('openSubmission', function(dashboardPanel, submissionTitle) {
    cy.get('div[data-pc-section="panel"]').first().within(() => {
        cy.get('div').first().then($el => {
            if ($el.attr('aria-expanded') === 'false') {
                $el.click();
                cy.wait(500);
            }
        });
        cy.contains('span', dashboardPanel).click();
    });

    cy.contains('span', submissionTitle).parent().parent().within(() => {
        cy.contains('button', 'View').click();
    });
    cy.waitJQuery();
});