<?php
class ModelExtensionThemeOcUltra extends Model {
    public function getNewsletters($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "oc_ultra_newsletter";

        $sql .= " ORDER BY date_added DESC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalNewsletters() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "oc_ultra_newsletter");

        return $query->row['total'];
    }
}
