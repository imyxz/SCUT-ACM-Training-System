<?php
/**
 * User: imyxz
 * Date: 2017-08-03
 * Time: 10:39
 * Github: https://github.com/imyxz/
 */
class tag_model extends SlimvcModel
{
    function newTag($tag_name,$tag_desc)
    {
        if(!$this->queryStmt("insert into problem_tags set tag_name=?,tag_desc=?,tag_count=0",
            "ss",
            $tag_name,$tag_desc))   return false;
        return $this->InsertId;
    }
    function addTagAlias($tag_id,$alias_name)
    {
        return $this->queryStmt("insert into problem_tags_alias set relative_tag_id=?,alias_name=?",
            "is",
            $tag_id,
            $alias_name);
    }
    function getTagInfoByTagName($tag_name)
    {
        return $this->queryStmt("select * from problem_tags where tag_name=? limit 1",
            "s",
            $tag_name)->row();
    }
    function getTagInfoByTagAlias($alias_name)
    {
        return $this->queryStmt("select problem_tags.*,problem_tags_alias.alias_id from problem_tags,problem_tags_alias
                                  where problem_tags_alias.alias_name=? and problem_tags.tag_id=problem_tags_alias.alias_id limit 1",
            "s",
            $alias_name)->row();
    }
    function getProblemTags($problem_id)
    {
        return $this->queryStmt("select problem_tag_relation.tag_id,problem_tags.tag_name from problem_tag_relation,problem_tags where problem_tag_relation.problem_id=? and problem_tags.tag_id=problem_tag_relation.tag_id",
            "i",
            $problem_id)->all();
    }
    function addProblemTag($problem_id,$tag_id)
    {
        if(!$this->queryStmt("insert into problem_tag_relation set problem_id=?,tag_id=?",
            "ii",
            $problem_id,
            $tag_id))   return false;
        return $this->queryStmt("update problem_tags set tag_count=tag_count+1 where tag_id=?",
            "i",
            $tag_id);
    }
    function isProblemHasTag($problem_id,$tag_id)
    {
        $row=$this->queryStmt("select * from problem_tag_relation where problem_id=? and tag_id=? limit 1",
            "ii",
            $problem_id,
            $tag_id)->row();
        if(!$row)
            return false;
        return $row;
    }
    function getTagProblems($tag_id,$page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select problem_tag_relation.problem_id,problem_info.memory_limit,problem_info.problem_title,problem_info.time_limit,problem_info.problem_identity,oj_site_info.oj_name,problem_info.add_time from problem_tag_relation,problem_info,oj_site_info
                                  where problem_tag_relation.tag_id=? and problem_info.problem_id=problem_tag_relation.problem_id and oj_site_info.oj_id=problem_info.oj_id limit ?,?",
            "iii",
            $tag_id,
            $start,
            $limit)->all();
    }
    function getAllTag()
    {
        return $this->query("select problem_tags.tag_count,problem_tags.tag_name from problem_tags order by tag_count DESC ")->all();
    }
}