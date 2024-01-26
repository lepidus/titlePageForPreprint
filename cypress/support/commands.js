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