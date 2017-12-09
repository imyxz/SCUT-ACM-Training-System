import Materialize from 'materialize-css/dist/js/materialize.min.js'
import $ from 'jquery'
export function toast (message, time = 2000) {
  Materialize.toast(message, time)
}
export function handleResponseError (data) {
  if (data.err_msg !== undefined) {
    toast(data.err_msg, 2000)
    return data
  } else {
    toast('网络请求错误', 2000)
    return {status: -1, err_msg: '网络请求错误', err_reason: data}
  }
}
export function setBackgroundPic (picUrl) {
  $('body').css('background-image', 'url(' + picUrl.replace(/'/g, '\\\'') + ')')
  $('body').css('background-repeat', 'no-repeat')
  $('body').css('background-attachment', 'fixed')
  $('body').css('background-size', 'cover')
  $('body').css('opacity', 0.9)
}
export function ramFilter (val) {
  val = parseFloat(val)
  let unit = 'B'
  if (val >= 1000) {
    val /= 1024
    unit = 'KB'
    if (val >= 1000) {
      val /= 1024
      unit = 'MB'
      if (val >= 1000) {
        val /= 1024
        unit = 'GB'
        if (val >= 1000) {
          val /= 1024
          unit = 'TB'
        }
      }
    }
  }
  return val.toFixed(2) + ' ' + unit
}
export function timeFilter (val) {
  val = parseFloat(val)
  let unit = 'ms'
  if (val >= 1000) {
    val /= 1000
    unit = 's'
  }
  return val.toFixed(2) + ' ' + unit
}
export function secondToHour (second) {
  let tmp = parseInt(second)
  tmp = tmp / 60
  tmp = tmp.toFixed(0)
  return (tmp / 60).toFixed(1) + ' 小时'
}
let NoPenaltyState = [
  0, // failed
  3, // CE
  15, // TESTING
  18 // IN_QUEUE
]
export function isAcStatusCalPenalty (state) {
  return !NoPenaltyState.includes(parseInt(state))
}
export function getAcTime (sec) {
  let second, minute, hour
  sec = parseInt(sec)
  second = sec % 60
  sec /= 60
  sec = Math.floor(sec)
  minute = sec % 60
  sec /= 60
  sec = Math.floor(sec)
  hour = sec
  return addZero(hour) + ':' + addZero(minute) + ':' + addZero(second)
}
export function addZero (val) {
  if (val === 0) {
    return '00'
  } else if (val < 10) {
    return '0' + val.toString()
  } else {
    return val.toString()
  }
}
export function fromUnixTime (second) {
  let time = new Date(second * 1000)
  return time.getFullYear() + '/' + (time.getMonth() + 1) + '/' + (time.getDate()) + ' ' + addZero(time.getHours()) + ':' + addZero(time.getMinutes())
}
