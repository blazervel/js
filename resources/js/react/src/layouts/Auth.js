import React from 'react'
import { Dashboard, Card, Container, ApplicationLogo } from '..'

export const Auth = ({ children, ...props }) => (
  <Dashboard {...props} sidebar={false} topbar={false}>
    <Container xs>

      <div className="flex justify-center">
        <ApplicationLogo lg />
      </div>

      <Card className="mt-6">
        {children}
      </Card>

    </Container>
  </Dashboard>
)
