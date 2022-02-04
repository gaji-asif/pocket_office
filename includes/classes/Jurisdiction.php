<?php

class Jurisdiction extends AssureClass {
    public $jurisdiction_id;
    public $account_id;
    public $location;
    public $midroof;
    public $midroof_timing;
    public $ladder;
    public $permit_url;
    public $permit_days;
    public $website_of_jurisdiction;
    public $roofing_handout; 
    public $phone_number_of_jurisdiction; 
    public $email_of_jurisdiction ;
    public $code_enforced ;
    public $codevalue ;
    public $link_to_code_book_irc ;
    public $commercial_ibc; 
    public $link_to_code_book_ibc ;
    public $link_to_iecc ;
    public $drip_edge_rakes ;
    public $drip_edge_eaves ;
    public $valley_liner ;
    public $step_flashing ;
    public $kickouts_required ; 
    public $insulation_wall ; 
    public $insulation_ceiling  ;
    public $threetabs_ok ; 
    public $require_ir_shingle ; 
    public $ice_and_water_shield_required  ;
    public $plank_deck_max_gap; 
    public $allow_shakes ; 
    public $class_a_requirement  ;
    public $ventilation_enforced ;
    public $drip_edge_rakes_commercial; 
    public $drip_edge_eaves_commercial ;
    public $valley_liner_commercial ;
    public $step_flashing_commercial ;
    public $kickouts_required_commercial;
    public $insulation_wall_commercial;
    public $insulation_ceiling_commercial;
    public $threetabs_ok_commercial;
    public $require_ir_shingle_commercial;
    public $ice_and_water_shield_required_commercial;
    public $plank_deck_max_gap_commercial;
    public $allow_shakes_commercial;
    public $class_a_requirement_commercial;
    public $ventilation_enforced_commercial;
    public $notes;
    
    
   protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('jurisdiction', $id);
         $record2 =DBUtil::getRecord('jurisdiction_additionals', $id, 'jurisdiction_id');
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('jurisdiction additionals not found');
            } else {
                return FALSE;
            }
        }
        //print_r($record2);die;
        list($this->jurisdiction_id,$this->account_id,$this->location,$this->midroof,$this->midroof_timing,$this->ladder, $this->permit_url, $this->permit_days) = array_values($record);
        $this->build($record);
         list($this->jurisdiction_additionals_id,$this->jurisdiction_id,$this->website_of_jurisdiction ,$this->roofing_handout ,$this->phone_number_of_jurisdiction ,$this->email_of_jurisdiction, $this->code_enforced ,$this->codevalue ,$this->link_to_code_book_irc ,
$this->commercial_ibc ,$this->link_to_code_book_ibc ,$this->link_to_iecc, $this->drip_edge_rakes ,$this->drip_edge_eaves ,$this->valley_liner ,
$this->step_flashing ,$this->kickouts_required  ,$this->insulation_wall , $this->insulation_ceiling  ,$this->threetabs_ok  ,$this->require_ir_shingle  ,$this->ice_and_water_shield_required  ,$this->plank_deck_max_gap, $this->allow_shakes , $this->class_a_requirement , $this->ventilation_enforced ,$this->drip_edge_rakes_commercial ,$this->drip_edge_eaves_commercial ,$this->valley_liner_commercial ,$this->step_flashing_commercial ,$this->kickouts_required_commercial,$this->insulation_wall_commercial,$this->insulation_ceiling_commercial,$this->threetabs_ok_commercial,$this->require_ir_shingle_commercial,$this->ice_and_water_shield_required_commercial,$this->plank_deck_max_gap_commercial,$this->allow_shakes_commercial,$this->class_a_requirement_commercial,$this->ventilation_enforced_commercial,$this->notes) = array_values($record2);
$this->build($record2);
        
        
        return TRUE;
    }



    
    
    
     public function getTooltip() {
        $info = array(
            'Title' => $this->location,
            'URL' => $this->permit_url,
            'Website of jurisdiction' => $this->website_of_jurisdiction ,
            'Roofing Handout' =>$this->roofing_handout ,
            'Phone number of jurisdiction' => $this->phone_number_of_jurisdiction ,
            'Email of jurisdiction' =>$this->email_of_jurisdiction, 
            'Code enforced?' =>$this->code_enforced ,
            'Residential Choose Codes' =>$this->codevalue ,
            'Link to code book IRC' =>$this->link_to_code_book_irc ,
            'Commercial IBC' =>$this->commercial_ibc ,
            'Link to code book IBC' =>$this->link_to_code_book_ibc ,
            'Link to IECC' =>$this->link_to_iecc, 
             'Drip Edge Rakes' =>$this->drip_edge_rakes ,
             'Drip Edge Eaves' =>$this->drip_edge_eaves ,
             'Valley Liner' =>$this->valley_liner ,
             'Step Flashing' =>$this->step_flashing ,
             'Kickouts Required?' =>$this->kickouts_required  ,
             'Insulation Wall' =>$this->insulation_wall , 
             'Insulation Ceiling ' =>$this->insulation_ceiling  ,
             '3-Tabs ok?' =>$this->threetabs_ok  ,
             'Require IR shingle?' =>$this->require_ir_shingle  ,
             'Ice and Water Shield required?' =>$this->ice_and_water_shield_required  ,
             'Plank Deck Max Gap' =>$this->plank_deck_max_gap, 
             'Allow Shakes?' =>$this->allow_shakes , 
             'Class A requirement?' =>$this->class_a_requirement , 
             'Ventilation Enforced?' =>$this->ventilation_enforced ,
             'Drip Edge Rakes-c' =>$this->drip_edge_rakes_commercial ,
             'Drip Edge Eaves-c' =>$this->drip_edge_eaves_commercial ,
             'Valley Liner-c' =>$this->valley_liner_commercial ,
             'Step Flashing-c' =>$this->step_flashing_commercial ,
             'Kickouts Required?-c' =>$this->kickouts_required_commercial,
             'Insulation Wall-c' =>$this->insulation_wall_commercial,
             'Insulation Ceiling-c' =>$this->insulation_ceiling_commercial,
             '3-Tabs ok?-c' =>$this->threetabs_ok_commercial,
             'Require IR shingle?-c' =>$this->require_ir_shingle_commercial,
             'Ice and Water Shield required?-c' =>$this->ice_and_water_shield_required_commercial,
             'Plank Deck Max Gap-c' =>$this->plank_deck_max_gap_commercial,
             'Allow Shakes?-c' =>$this->allow_shakes_commercial,
             'Class A requirement?-c' =>$this->class_a_requirement_commercial,
             'Ventilation Enforced?-c' =>$this->ventilation_enforced_commercial,
             'notes' =>$this->notes,
        );
            
        echo ViewUtil::loadView('tooltip2', array('info' => $info));
    }
    
}