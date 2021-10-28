
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
    });
})
