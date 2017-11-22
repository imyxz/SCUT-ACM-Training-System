const PREFIX = '/api/'
export default {
  user: {
    updateAcStatus: PREFIX + 'userAPI/updateAcStatus/',
    addTeam: PREFIX + 'userAPI/addTeam/',
    getAllTeam: PREFIX + 'userAPI/getAllTeam/',
    bindPlayer: PREFIX + 'userAPI/bindPlayer/',
    getUserInfo: PREFIX + 'userAPI/getUserInfo/',
    updateUserInfo: PREFIX + 'userAPI/updateUserInfo/',
    getBgPic: PREFIX + 'userAPI/getBgPic/',
    getUserBgPic: PREFIX + 'userAPI/getUserBgPic/',
    logOut: PREFIX + 'userAPI/logOut/'
  },
  contest: {
    getAllContest: PREFIX + 'contestAPI/getAllContest/',
    getContestList: PREFIX + 'contestAPI/getContestList/',
    getContestSummary: PREFIX + 'contestAPI/getContestSummary/',
    addContestFromCSV: PREFIX + 'contestAPI/addContestFromCSV/',
    updatePlayerAcStatus: PREFIX + 'contestAPI/updatePlayerAcStatus/'
  },
  vjudge: {
    problem: {
      getProblemInfo: PREFIX + 'vJudgeAPI/getProblemInfo/',
      getProblemAcRank: PREFIX + 'vJudgeAPI/getProblemAcRank/',
      getAllProblem: PREFIX + 'vJudgeAPI/getAllProblem/',
      getProblemByInfo: PREFIX + 'vJudgeAPI/getProblemByInfo/',
      submitCode: PREFIX + 'vJudgeAPI/submitCode/'
    },
    problemList: {
      getListProblems: PREFIX + 'vJudgeAPI/getListProblems/',
      newList: PREFIX + 'vJudgeAPI/newList/',
      updateList: PREFIX + 'vJudgeAPI/updateList/',
      getAllList: PREFIX + 'vJudgeAPI/getAllList/'
    },
    problemTag: {
      getTagList: PREFIX + 'vJudgeAPI/getTagList/',
      getAllTags: PREFIX + 'vJudgeAPI/getAllTags/'
    },
    job: {
      getJobStatus: PREFIX + 'vJudgeAPI/getJobStatus/',
      getJobSourceCode: PREFIX + 'vJudgeAPI/getJobSourceCode/',
      setJobShare: PREFIX + 'vJudgeAPI/setJobShare/'
    },
    status: {
      getMyStatus: PREFIX + 'vJudgeAPI/getMyStatus/'
    },
    contest: {
      getContestInfo: PREFIX + 'vJudgeAPI/getContestInfo/',
      joinContest: PREFIX + 'vJudgeAPI/joinContest/',
      getContestProblem: PREFIX + 'vJudgeAPI/getContestProblem/',
      submitContestJob: PREFIX + 'vJudgeAPI/submitContestJob/',
      getContestSubmission: PREFIX + 'vJudgeAPI/getContestSubmission/',
      newContest: PREFIX + 'vJudgeAPI/newContest/',
      getContestStatus: PREFIX + 'vJudgeAPI/getContestStatus/',
      getAllContest: PREFIX + 'vJudgeAPI/getAllContest/'
    },
    spider: {
      getSpiderStatus: PREFIX + 'vJudgeAPI/getSpiderStatus/'
    }
  }
}
