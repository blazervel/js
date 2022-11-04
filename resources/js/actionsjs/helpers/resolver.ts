class Resolver {
  
  _resolvers: Array<{ key: string, resolve: (value: any) => void }> = []

  create(key: string): Promise<any> {
    return new Promise(resolve => this._resolvers.push({ key, resolve }))
  }

  run(resolverKey: string, resolveWith: any): void {
    this
      ._resolvers
      .filter(({ key, resolve }) => key === resolverKey)
      .map(({ key, resolve }) => resolve(resolveWith))
  }

  runAll(resolveWiths: object) {
    Object.entries(resolveWiths).map(([key, resolveWith]) => (
      this.run(key, resolveWith)
    ))
  }

}

export default Resolver