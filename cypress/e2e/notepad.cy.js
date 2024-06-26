describe('Notepad tests', () => {
  before(() => {
    cy.request('/logout')
      .request('/login')
      .its('body')
      .then(body => {
        const $html = Cypress.$(body)
        const csrf = $html.find('input[name=_csrf_token]').val()

        cy.loginByCSRF(csrf).then(resp => {
          expect(resp.status).to.eq(200)
        })
      })
  })

  it('opens blank note', () => {
    // have notes return empty
    cy.intercept('GET', '/notes', { notes: [] })
    cy.visit('/')
      .contains('Add Note')
      .get('.notes-body')
      .should('have.value', '')

    cy.visit('/').get('.notes-body').type('Hello, Friend')
    cy.get('.notes-body').should('have.value', 'Hello, Friend')
  })
})
