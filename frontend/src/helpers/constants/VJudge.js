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
export const codeType = [
  {
    name: 'C',
    mode: 'c_cpp'
  },
  {
    name: 'C++',
    mode: 'c_cpp'
  },
  {
    name: 'java',
    mode: 'java'
  },
  {
    name: 'PHP7',
    mode: 'php'
  },
  {
    name: 'Pascal',
    mode: 'pascal'
  },
  {
    name: 'Python3',
    mode: 'python'
  }
]
export function getCodeTypeName (typeId) {
  return codeType[parseInt(typeId)].name
}
export function getCodeTypeMode (typeId) {
  return codeType[parseInt(typeId)].mode
}
export function getAcStatus (status) {
  return acStatus[parseInt(status)]
}
