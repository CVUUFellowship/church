<?php

class AboutController extends Zend_Controller_Action
{

        public function getPeople($list)
        {
            $positionsmap = new Application_Model_PositionsMapper();
            $peoplemap = new Application_Model_PeopleMapper();
            $people = array();
            foreach ($list as $position)
            {
                $where = array(
                    array('title', ' = ', $position),
                        );            
                $members = $positionsmap->fetchWhere($where);
                foreach ($members as $member)
                    $people[] = $peoplemap->find($member->contact1);
            }
            return $people;
        }


    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


    public function ministerAction()
    {
        return $this->render('minister');
    }


    public function leadershipAction()
    {
        $boardPositions=array("President", "Vice-President", "Secretary", "Treasurer", "Trustee at Large");
        $boardTitles=array("   President    ", " Vice-President  ", "    Secretary    ",
          "  Treasurer  ", "Trustee at Large ", "Trustee at Large ", "Trustee at Large ");
      
        $councilPositions=array("Operations Group Director", 
          "Membership Group Director", "Ministry Group Director", "Outreach Group Director",
          "Communications Group Director","Education Group Director");
        $councilTitles1=array("Operations", 
          "Membership", "Ministry", "Outreach", "Communications",  "Education");
        $councilTitles2=array("Group Director", 
          "Group Director", "Group Director", "Group Director", "Group Director", "Group Director");
      
        $staffPositions=array("Minister", "Administrator", "Director Of Religious Education (DRE)", "Choir Director / Accompanist");
        $staffTitles1=array("", "", "Director Of", "Choir Director /");
        $staffTitles2=array("Minister", "Administrator", "Religious Education", "Accompanist");

        $this->view->btitles = $boardTitles;
        $this->view->ctitles1 = $councilTitles1;
        $this->view->ctitles2 = $councilTitles2;
        $this->view->stitles1 = $staffTitles1;
        $this->view->stitles2 = $staffTitles2;

        $this->view->board = $this->getPeople($boardPositions);
        $this->view->council = $this->getPeople($councilPositions);
        $this->view->staff = $this->getPeople($staffPositions);

        return $this->render('leadership');
    }


    public function historyAction()
    {
        $this->view->head = "A Brief History";
        $this->view->paragraphs = array(
            "One evening in December of 1961, a handful of Unitarians got together at the now extinct 
            Redwood Lodge to talk about starting a fellowship. Mainly couples with young families, they 
            were hoping to find a place to voice their liberal views and provide religious education 
            for their children. With assistance from the UUA Extension, they formed the Tri-Valley UU 
            Fellowship and affiliated with the Unitarian Universalist Association in February of 1962.
             Members eagerly took the many workshops given by the Pacific Southwest District so they 
             could learn how to run this organization effectively. The Fellowship quickly grew in those 
             early years, attracting many teachers, writers and other creative members with its 
             stimulating lectures and programs. Members were energetic and visionary, having a spirit 
             that still prevails to this day. ",
            "From the very beginning, attaining our own facilities was important so we started a building 
            fund and in 1969 purchased a midtown residence that we had to sell in 1974 because of zoning 
            restrictions. Before and since, we have rented in many locations. On July 13, 2008 we held our 
            first service at our own church home at 3327 Old Conejo Road where we presently meet. ",
            "We have also had a number of part time ministers through the years and Rev. Dr. Betty Stapleford 
            was our second installed minister (the first took place in 1980) but she was our first full time 
            minister. The UUA Extension assisted us in this step and has taken an interest in our growth 
            throughout the years. Rev. Dr. Stapleford retired in June 2010 and the Rev. Helen Carroll was 
            our Interim Minister from August 2010 to June 2011.  The Rev. Lora Brandis began her association
            with us as our Settled Minister in August 2011. 
            ",
            "Ours has not been a history of steady growth but one that reflects our changing society. 
            Since the mid-90's, we have experienced unprecedented growth as people are searching for 
            community and meaningful lives without dogma. Throughout our history we have had many highs 
            and lows in membership and yet our spirit, energy, and caring has kept us alive over the 
            years. "
        );
    
        return $this->render('textpage');
    }
    
   
}

