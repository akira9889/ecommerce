const state = {
  user: {
    token: sessionStorage.getItem('TOKEN'),
    data: {}
  },
  countries: [],
  products: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: 10,
    total: 0
  },
  users: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: 10,
    total: 0
  },
  customers: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: 10,
    total: 0
  },
  orders: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: 10,
    total: 0
  },
  toast: {
    show: false,
    message: '',
    delay: 5000
  },
}

export default state
