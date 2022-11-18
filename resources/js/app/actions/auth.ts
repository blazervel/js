import Connection from '../helpers/connection'

export interface AuthAttemptProps {
  email: string
  password: string
}

export default ($app) => ({
  
  user() {
    const userId = this._currentUser()

    if (!userId) {
      return null
    }

    return $app.models.user.find(userId)
  },

  async _currentUser() {
    const response = await (new Connection('actions/auth-user'))._post({ namespace: 'blazervel' })
    
    return response.user || null
  },

  check() {
    return this._currentUser() !== null
  },

  async attempt({ email, password }: AuthAttemptProps) {
    const response = await (new Connection('actions/auth-attempt'))._post({ email, password, namespace: 'blazervel' })

    if (response.user || false) {
      return false
    }

    return $app.models.user.find(response.user)
  },

  async logout() {
    return await (new Connection('actions/auth-logout'))._post({ namespace: 'blazervel' })
  }
})