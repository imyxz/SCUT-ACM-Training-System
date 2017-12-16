import {get} from '../../network'
import API from '../../constants/api'
export function getAllTags (cache = true) {
  return get(API.vjudge.problemTag.getAllTags, {}, cache)
}
export function getTagList (tagName, page, cache = true) {
  return get(API.vjudge.problemTag.getTagList, {
    name: tagName,
    page: page
  }, cache)
}
