export var contestType = [
  '同步开始',
  '异步开始'
]
const acStatus = [
  'failed',
  'Accept',
  'Wrong Answer',
  'Compilation Error',
  'Runtime Error',
  'Wrong Answer',
  'Presentation Error',
  'Time Limit Exceeded',
  'Memory Limit Exceeded',
  'Idleness Limit Exceeded',
  'Security Violated',
  'Crashed',
  'Input Preparation Crashed',
  'Challenged',
  'Skipped',
  'Testing',
  'Rejected',
  'Output Limit Exceeded',
  'In Queue'
]
export function getAcStatus (status) {
  return acStatus[parseInt(status)]
}
