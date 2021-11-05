import 'core-js/modules/es.regexp.exec';

function createDirectory(directory) {
    let commandLineForCreateDirectory = 'mkdir ' + directory;
    cy.exec(commandLineForCreateDirectory).its('code').should('eq', 0);
}

function downloadFile(receivedFile, url) {
    let commandLineForDownload = 'curl -o ' + receivedFile + url;
    cy.exec(commandLineForDownload).its('code').should('eq', 0);
}

function changeOnePageOfPdfToText(receivedFile, textFile) {
    let CommandLineForPdfToText = 'pdftotext -f 1 -l 1 ' + receivedFile + textFile;
    cy.exec(CommandLineForPdfToText).its('code').should('eq', 0);
}

function removeDirectory(directory) { 
    let CommandLineForRemoveDirectory = 'rm -R ' + directory;
    cy.exec(CommandLineForRemoveDirectory).its('code').should('eq', 0);
}

function checkRelationAfterChoice(textFile) { 
    cy.readFile(textFile).should('contain','Preprint has been submitted for publication in journal');
}

describe('Title Page Plugin relation test', function() {
    it('Change relation tests', function() {
        cy.visit(Cypress.env('baseUrl') + 'index.php/f/submissions');
        cy.get('input[id=username]').click();
        cy.get('input[id=username]').type(Cypress.env('OPSAdminUsername'), { delay: 0 });
        cy.get('input[id=password]').click();
        cy.get('input[id=password]').type(Cypress.env('OPSAdminPassword'), { delay: 0 });
        cy.get('button[class=submit]').click();

        cy.get('#archive-button').click();
        cy.get('.listPanel__itemActions > .pkpButton').click();
        cy.get('#publication-button').click();
        cy.get('.pkpPublication__relation > .pkpDropdown > .pkpButton').click();

        cy.get('input[value^=2]').check();
        cy.get('button[class=pkpButton]').contains('Save').click();
        cy.contains('Save');

        cy.get('a.pkpButton').click();
        cy.get('.obj_galley_link').click();
        cy.get('.download').then((anchor) => {
            const url = anchor.attr('href');
            const directory = './plugins/generic/titlePageForPreprint/cypress/tests/result/';
            const receivedFile = directory +'receivedFile.pdf ';
            const textFile = directory + 'titlePage.txt';

            createDirectory(directory);

            downloadFile(receivedFile, url);
            
            changeOnePageOfPdfToText(receivedFile, textFile);

            checkRelationAfterChoice(textFile);

            removeDirectory(directory);
        });

    });
})

