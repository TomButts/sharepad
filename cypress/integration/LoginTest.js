let testUser = {
    email: 'test@test.com',
    password: 'password'
}

describe('Test Login Features', () => {
  it('login page displays (when unauthenticated)', () => {
    // index should redirect to login page
    cy.visit('/')
    cy.url().should('include', '/login')

    cy.contains('Email address')

    cy.get('#email')
      .type(testUser.email)
      .should('have.value', 'test@test.com')


    cy.contains('Password')

    cy.get('#password')
      .type(testUser.password)


    cy.contains('Sign in')
      .click()
    
    // send user to index of spa
    cy.url().should('include', '/')

    cy.contains('Add Note')
  })

  it('user can sign in', () => {
    cy.visit('/')

    cy.get('#email')
      .type(testUser.email)
      .should('have.value', 'test@test.com')

    cy.get('#password')
      .type(testUser.password)

    cy.contains('Sign in')
      .click()
    
    // send user to index of spa
    cy.url().should('include', '/')

    cy.contains('Add Note')
  })
})