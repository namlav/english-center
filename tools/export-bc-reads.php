<?php

declare(strict_types=1);

// Exports only the five attributed practice sets, never users or learner history.
$root = dirname(__DIR__);
$manifest = json_decode(file_get_contents($root.'/database/data/bc-reads-practice.json'), true, 512, JSON_THROW_ON_ERROR);
$source = new PDO('sqlite:'.($argv[1] ?? $root.'/database/learning-demo.sqlite'));
$source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
function row(PDO $source, string $sql, array $parameters): array
{
    $query = $source->prepare($sql);
    $query->execute($parameters);

    return $query->fetch(PDO::FETCH_ASSOC) ?: throw new RuntimeException('Missing seeded practice content.');
}
function sqlValue(mixed $value): string
{
    if ($value === null) {
        return 'NULL';
    }
    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }

    return "'".str_replace(['\\', "\0", "\n", "\r", "\x1a", "'"], ['\\\\', '\\0', '\\n', '\\r', '\\Z', "''"], (string) $value)."'";
}
function insertSql(string $table, array $record, array $variables = [], bool $newPaperOnly = false, bool $upsert = false): string
{
    unset($record['id']);
    $columns = implode(', ', array_map(static fn (string $column): string => '`'.$column.'`', array_keys($record)));
    $values = implode(', ', array_map(static fn (string $column): string => $variables[$column] ?? sqlValue($record[$column]), array_keys($record)));
    $insert = 'INSERT INTO `'.$table.'` ('.$columns.') ';
    $insert .= $newPaperOnly ? 'SELECT '.$values.' WHERE @BC_NEW_PAPER = 1' : 'VALUES ('.$values.')';

    return $insert.($upsert ? ' ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)' : '').";\n";
}
$sql = <<<'SQL'
-- BC Reads: 5 bo luyen, 60 cau hoi, 60 dap an, 63 lua chon.
-- Import vao database du an da co cac bang online (goi 37/39 bang).
-- Chi them noi dung; khong DROP/DELETE, khong chen tai khoan hay lich su hoc.
-- Nhap lai se bo qua ca bo de da ton tai theo slug, giu noi dung da bien tap.
-- Shantel Ivits (2015), BCcampus: BC Reads – Course Pack 6. CC BY 4.0.
-- https://opentextbc.ca/abealf6/ ; https://creativecommons.org/licenses/by/4.0/
-- Download for free from the B.C. Open Collection: https://collection.bccampus.ca/
-- Adapted blanks/choices; added Vietnamese instructions/explanations and spelling variants.
-- Chua thu import tren may chu MySQL/MariaDB that; da kiem thu seeder bang SQLite.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @BC_OLD_SQL_MODE = @@SESSION.sql_mode;
SET @BC_OLD_TIME_ZONE = @@SESSION.time_zone;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_AUTO_VALUE_ON_ZERO';
SET SESSION time_zone = '+00:00';
START TRANSACTION;

SQL;
$category = row($source, 'SELECT * FROM learning_categories WHERE slug = ?', ['english-foundation']);
$collection = row($source, 'SELECT * FROM exam_collections WHERE slug = ?', [$manifest['collection']['slug']]);
$sql .= insertSql('learning_categories', $category, upsert: true);
$sql .= "SET @BC_CATEGORY = (SELECT id FROM learning_categories WHERE slug = 'english-foundation');\n";
$sql .= insertSql('exam_collections', $collection, upsert: true);
$sql .= 'SET @BC_COLLECTION = (SELECT id FROM exam_collections WHERE slug = '.sqlValue($manifest['collection']['slug']).");\n\n";
$questionCount = 0;
$optionCount = 0;
$preview = '# 5 bộ luyện tiếng Anh — câu hỏi và đáp án'."\n\n".$manifest['source']['attribution']."\n\n".$manifest['source']['license_url']."\n\n".$manifest['source']['adaptation']."\n";
foreach ($manifest['sets'] as $set) {
    $paper = row($source, 'SELECT * FROM exam_papers WHERE slug = ?', [$set['slug']]);
    $paper['created_by'] = null;
    $sql .= '-- '.$set['title']."\n";
    $sql .= 'SET @BC_NEW_PAPER = (SELECT COUNT(*) = 0 FROM exam_papers WHERE slug = '.sqlValue($set['slug']).");\n";
    $sql .= insertSql('exam_papers', $paper, ['learning_category_id' => '@BC_CATEGORY', 'exam_collection_id' => '@BC_COLLECTION'], newPaperOnly: true);
    $sql .= 'SET @BC_PAPER = (SELECT id FROM exam_papers WHERE slug = '.sqlValue($set['slug']).");\n";
    $section = row($source, 'SELECT * FROM exam_sections WHERE exam_paper_id = ? AND position = 1', [$paper['id']]);
    $sql .= insertSql('exam_sections', $section, ['exam_paper_id' => '@BC_PAPER'], newPaperOnly: true);
    $sql .= "SET @BC_SECTION = (SELECT id FROM exam_sections WHERE exam_paper_id = @BC_PAPER AND position = 1);\n";
    $group = row($source, 'SELECT * FROM question_groups WHERE exam_section_id = ? AND position = 1', [$section['id']]);
    $sql .= insertSql('question_groups', $group, ['exam_section_id' => '@BC_SECTION'], newPaperOnly: true);
    $sql .= "SET @BC_GROUP = (SELECT id FROM question_groups WHERE exam_section_id = @BC_SECTION AND position = 1);\n";
    $preview .= "\n## ".$set['title']."\n\n".$set['instructions']."\n\n".$set['lesson']."\n\nNguồn: ".$set['source_url']."\n";
    foreach ($set['questions'] as $index => $item) {
        $question = row($source, 'SELECT * FROM questions WHERE question_group_id = ? AND position = ?', [$group['id'], $index + 1]);
        $sql .= insertSql('questions', $question, ['question_group_id' => '@BC_GROUP', 'exam_section_id' => '@BC_SECTION'], newPaperOnly: true);
        $sql .= 'SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = '.($index + 1).");\n";
        $query = $source->prepare('SELECT * FROM question_options WHERE question_id = ? ORDER BY position');
        $query->execute([$question['id']]);
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $option) {
            $sql .= insertSql('question_options', $option, ['question_id' => '@BC_QUESTION'], newPaperOnly: true);
            $optionCount++;
        }
        $key = row($source, 'SELECT * FROM question_answer_keys WHERE question_id = ?', [$question['id']]);
        if (json_decode($key['accepted_answers'], true, 512, JSON_THROW_ON_ERROR) !== $item['answers'] || $question['prompt'] !== $item['prompt']) {
            throw new RuntimeException('Local edits detected: export must match the attributed manifest.');
        }
        $sql .= insertSql('question_answer_keys', $key, ['question_id' => '@BC_QUESTION'], newPaperOnly: true)."\n";
        $preview .= "\n".($index + 1).'. '.$item['prompt']."\n\n";
        foreach ($item['options'] ?? [] as $label => $text) {
            $preview .= '   '.$label.'. '.$text."\n";
        }
        $preview .= "\n   Đáp án: ".implode(' / ', $item['answers']).'. '.$item['explanation']."\n\n   Câu nguồn: ".$item['source_item'].' — '.($item['source_url'] ?? $set['source_url'])."\n";
        $questionCount++;
    }
}
if ($questionCount !== 60 || $optionCount !== 63) {
    throw new RuntimeException('Unexpected bank size.');
}
$sql .= "COMMIT;\nSET SESSION time_zone = @BC_OLD_TIME_ZONE;\nSET SESSION sql_mode = @BC_OLD_SQL_MODE;\n";
file_put_contents($root.'/database/sql/bc_reads_5_bo_60_cau.sql', $sql);
file_put_contents($root.'/docs/BC-READS-CAU-HOI-DAP-AN.md', $preview);
echo json_encode(['sets' => 5, 'questions' => $questionCount, 'options' => $optionCount, 'sql_bytes' => strlen($sql)], JSON_UNESCAPED_SLASHES).PHP_EOL;
