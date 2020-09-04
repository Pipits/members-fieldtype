<?php
/**
 * A field type for selecting Members
 * 
 * @author Hussein Al Hammad
 * 
 */
class PerchFieldType_members extends PerchAPI_FieldType {

    public function render_inputs($details=array()) {    
        $id  = $this->Tag->input_id();
        $val = $selected_members = [];
        $load_all = $this->Tag->all();

        

        $API = new PerchAPI(1.0, 'perch_members');
        $Members = new PerchMembers_Members($API);
        $Members->created_date_column = 'memberCreated';
        
         
        if (isset($details[$id]) && $details[$id]!='') {
            $val = $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()); 

            if(!$load_all) {
                $selected_members = $Members->get_by('memberID', $details[$id]);
            }
        }
        


        if($load_all) {
            $members = $Members->all();
        } else {
            $members = $Members->get_recent();
        }     


        if(count($selected_members)) {
            $selected_ids = array_map(function($item) {return $item->id();}, $selected_members);

            foreach($members as $key => $Member) {
                if(in_array($Member->id(), $selected_ids)) {
                    unset($members[$key]);
                }
            }

            $members = array_merge($members, $selected_members);
        }

        

        $opts   = array();
        $opts[] = array('label'=>'', 'value'=>'');

        if (PerchUtil::count($members)) {
            foreach($members as $Member) {
                $opts[] = array('label' => $this->member_name($Member) . ' - ' . $Member->memberEmail(), 'value'=>$Member->id());
            }
        }

        
        
        $classes= 'input-simple m';
        $attributes = ' data-members="choices"';
        
        if ($this->Tag->max()) {
			$attributes .= ' data-max="'.(int)$this->Tag->max().'"';
        }
        
        if ($this->Tag->placeholder()) {
            $attributes .= ' placeholder="'.$this->Tag->placeholder().'"';
        } else {
            $attributes .= ' placeholder=" "';
        }
        
        $attributes = trim($attributes);

        
        
        if (PerchUtil::count($opts)) {
        	$s = $this->Form->select($id, $opts, $val, $classes, true, $attributes);
        } else {
        	$s = '-';
        }
        

        return $s;
    }
    




    public function get_raw($post=false, $Item=false) {
        $store  = array();
        $id     = $this->Tag->id();

        if ($post===false) $post = $_POST;
        
        if (isset($post[$id])) {
            $this->raw_item = $post[$id];
            $members = $this->raw_item;

            foreach($members as $id) {
                $store[] = $id;
            }
        }

        return $store;
    }
    




    public function get_processed($raw=false) {    
        if ($raw === false) {
            $raw = $this->get_raw();
        }
        
        $value = $raw;

        if (is_array($value)) {
            return implode(',', $value);
        }
        
        return $value;
    }
    




    public function get_index($raw=false) {
        if ($raw === false) {
            $raw = $this->get_raw();
        }
        
        $id = $this->Tag->id();
        $value = $raw;
        $result = array();

        if (is_array($value)) {
            $value = implode(',', $value);
        }
        
        $result[] = ['key' => $id, 'value' => $value];
        return $result;
    }




    public function get_search_text($raw=false) {
		return false;
    }





    public function render_admin_listing($raw=false) {
        if ($raw === false) {
            $raw = $this->get_raw();
        }
        
        $value = $raw;

        if (is_array($value)) {
            array_walk($value, function(&$item) {
                $item = $this->member_name_from_id($item);
            });

            return implode(', ', $value);
        }

		return $value;
    }






    public function get_content_summary($details=array(), $Template) {
        if (!PerchUtil::count($details)) return '';

        $id  = $this->Tag->input_id();
        $value = '';
        
        if (isset($details[$id]) && $details[$id]!='') {
            $value = $details[$id];

            if (is_array($value)) {
                unset($value['_default']);
                array_walk($value, function(&$item) {
                    $item = $this->member_name_from_id($item);
                });
    
                return implode(', ', $value);
            }
        }        

        return PerchUtil::html($value, true);
    }






    private function member_name($Member) {
        if(is_object($Member)) {
            $name = $Member->name();
            if($name) return $name;
            return $Member->first_name() . ' ' . $Member->last_name();
        }


        return false;
    }




    private function member_name_from_id($id) {
        $Members = new PerchMembers_Members();
        return $this->member_name($Members->find($id));
    }




    public function add_page_resources()
    {
        $Perch = Perch::fetch();
        $Perch->add_css("https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css");
        $Perch->add_javascript("https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js");

        $js_path = '/addons/fieldtypes/members/js/app.js';
        $Perch->add_javascript( PERCH_LOGINPATH."$js_path?v=" . filemtime(PerchUtil::file_path(PERCH_PATH . $js_path)) );
    }
}