export interface BlazerelConfigProps {
  certsPath?: string
}

export interface BlazervelComposerConfigProps {
  type: string
  url: string
  path: string
  options: {
    symlink: boolean
  }
}

export interface ComposerConfigProps {
  repositories: {
    blazervel: BlazervelComposerConfigProps
  }
}

export interface AuthAttemptProps {
  email: string
  password: string
}