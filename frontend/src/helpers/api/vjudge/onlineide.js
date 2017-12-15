import {get, postJson} from '../../network'
import API from '../../constants/api'
export function submitJob (sourceCode, inputCode, codeType) {
  return postJson(API.vjudge.onlineide.submitJob, {}, {
    source_code: sourceCode,
    input_code: inputCode,
    code_type: codeType
  })
}
export function getJobResult (jobId, cache = false) {
  return get(API.vjudge.onlineide.getJobResult, {job_id: jobId}, cache)
}
export function getCodeTypeDefaultCode (codeType, cache = true) {
  return get(API.vjudge.onlineide.getCodeTypeDefaultCode, {codeType: codeType}, cache)
}
export function saveDraft (draftTitle, sourceCode, codeType, isAutoSave) {
  return postJson(API.vjudge.onlineide.saveDraft, {}, {
    draft_title: draftTitle,
    source_code: sourceCode,
    is_autosave: isAutoSave,
    code_type: codeType
  })
}
export function shareCode (sourceCode, codeType) {
  return postJson(API.vjudge.onlineide.shareCode, {}, {
    source_code: sourceCode,
    code_type: codeType
  })
}
export function formatCode (sourceCode, codeType) {
  return postJson(API.vjudge.onlineide.formatCode, {}, {
    source_code: sourceCode,
    code_type: codeType
  })
}
export function getUserDraft (cache = true) {
  return get(API.vjudge.onlineide.getUserDraft, {}, cache)
}
export function getDraftCode (draftId, cache = true) {
  return get(API.vjudge.onlineide.getDraftCode, {id: draftId}, cache)
}
export function getShareCode (shareId, cache = true) {
  return get(API.vjudge.onlineide.getShareCode, {id: shareId}, cache)
}
