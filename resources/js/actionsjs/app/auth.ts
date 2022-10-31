import Connection from '../helpers/connection'
import { AuthAttemptProps } from './types/auth'

export default ($app) => ({
  
  user() {
    const userId = this._currentUser()

    if (!userId) {
      return null
    }

    return $app.Models.User.find(userId)
  },

  async _currentUser() {
    const response = await (new Connection('actions/auth-user'))._post({ namespace: 'blazervel-actionsjs' })
    
    return response.user || null
  },

  check() {
    return this._currentUser() !== null
  },

  async attempt(props: AuthAttemptProps) {
    const response = await (new Connection('actions/auth-attempt'))._post({ email, password, namespace: 'blazervel-actionsjs' })

    if (response.user || false) {
      return false
    }

    return $app.Models.User.find(response.user)
  },

  async logout() {
    return await (new Connection('actions/auth-logout'))._post({ namespace: 'blazervel-actionsjs' })
  }
})