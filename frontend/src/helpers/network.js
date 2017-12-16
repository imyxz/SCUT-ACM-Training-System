import axios from 'axios'
import lodash from 'lodash'
import {handleResponseError} from './common'
let Cache = new Map()

function checkStatusCode (response) {
  if (!response || parseInt(response.status) !== 0) {
    return Promise.reject(response)
  } else {
    return Promise.resolve(response)
  }
}
function generateGetUrl (url, params = {}) {
  let result = url
  for (let key in params) {
    result += `${key}/${params[key]}/`
  }
  return result
}
export function get (url, params = {}, cache = true) {
  let target = generateGetUrl(url, params)
  if (cache && Cache.has(target)) {
    console.log('Hit cache:' + target)
    return Promise.resolve(lodash.cloneDeep(Cache.get(target)))
  } else {
    console.log('Fetching:' + target)
    return axios.get(target).then((response) => {
      return checkStatusCode(response.data).then((result) => {
        Cache.set(target, result)
        return Promise.resolve(lodash.cloneDeep(result))
      })
    }
    )
    .catch(reject => {
      // eslint-disable-next-line
      return Promise.reject(handleResponseError(reject))
    })
  }
}
export function postJson (url, params = {}, obj = {}) {
  let target = generateGetUrl(url, params)
  return axios.post(target, JSON.stringify(obj)).then((response) => {
    return checkStatusCode(response.data).then((result) => {
      return Promise.resolve(result)
    })
  })
  .catch(reject => {
    // eslint-disable-next-line
    return Promise.reject(handleResponseError(reject))
  })
}
