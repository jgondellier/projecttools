<?php
namespace GanttBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use GanttBundle\Models\JSONGanttConnector;

class DataController extends Controller
{
    /**
     * @Route("/getdata")
     */
    public function indexAction()
    {

        $res=mysql_connect("localhost","root","");
        mysql_select_db("gantt");

        $gantt = new JSONGanttConnector($res);
        $gantt->render_links("gantt_links","id","source,target,type");

        return $gantt->render_table(
            "gantt_tasks",
            "id",
            "start_date,duration,text,progress,sortorder,parent"
        );
    }
}
