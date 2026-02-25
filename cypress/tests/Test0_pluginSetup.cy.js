describe('Title Page for Preprint - Plugin setup', function () {
    it('Enables Title Page for Preprint plugin', function () {
		cy.login('dbarnes', null, 'publicknowledge');

		cy.get('nav').contains('Settings').click();
		cy.get('nav').contains('Website').click({ force: true });

		cy.waitJQuery();
		cy.get('#plugins-button').click();

		cy.get('input[id^=select-cell-titlepageforpreprintplugin]').check();
		cy.get('input[id^=select-cell-titlepageforpreprintplugin]').should('be.checked');
    });
});