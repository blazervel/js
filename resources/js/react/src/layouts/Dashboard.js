import { Head } from '@inertiajs/inertia-react'
import { Sidebar, Topbar, Container } from '..'

export function Dashboard({
  navigation = [],
  pageTitle,
  sidebar = true,
  fullWidth = false,
  className,
  children,
  ...props
}) {

  if (sidebar) {
    return (
      <div id="dashboard" className={`${className} bg-white dark:bg-chrome-900 min-h-screen`} {...props}>
        <Head title={pageTitle} />
        <Sidebar navigation={navigation}>
          <Container className="py-12">
            {children}
          </Container>
        </Sidebar>
      </div>
    )
  }

  return (
    <div
      id="dashboard"
      className={`${className} bg-white dark:bg-chrome-900 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0`}
      {...props}
    >
      <Head title={pageTitle} />
      <Topbar navigation={navigation} />
      <Container>
        {children}
      </Container>
    </div>
  )
}