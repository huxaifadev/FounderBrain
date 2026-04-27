<?php
require_once __DIR__ . '/../config.php';
requireLogin();
header('Content-Type: application/json');

$body     = json_decode(file_get_contents('php://input'),true);
$messages = $body['messages'] ?? [];
$mode     = $body['mode'] ?? 'chat';

$systems = [
'chat' => "You are FounderBrain, an elite AI Chief of Staff for early-stage startup founders. You are direct, sharp, and action-oriented.

When a founder pastes an email, Slack message, or situation:
1. Understand what's at stake immediately
2. Draft the exact reply or action needed
3. Flag anything they might be missing
4. Suggest next 1-2 actions

You understand: YC applications, investor relations, MRR, churn, runway, roadmaps, co-founder dynamics.
Format clearly. Use **bold** for key actions. Be concise. Never generic.",

'gmail' => "You are FounderBrain's Email Intelligence layer. Analyze this email and respond in this exact structure:

**URGENCY:** [high/medium/low] — [one sentence why]

**CONTEXTUAL SENTIMENT:** [2 sentences about tone and what the sender really wants]

**ACTION RECOMMENDED:** [1-2 sentences on what the founder should do]

**DRAFT REPLY:**
[Write a complete, professional reply the founder can send immediately. Start with Hi/Hello. Keep it under 100 words unless the email is complex. Sound human, not robotic.]",

'followup' => "You are FounderBrain's Auto Follow-up Engine.

Write a SHORT, warm, professional follow-up email. Rules:
- Max 60 words
- Sound human, not salesy
- Reference the original topic briefly
- End with a soft call to action
- Do NOT write a subject line
- Just write the body text directly",

'tasks' => "Extract all unfinished items from the founder's brain dump.
Return ONLY valid JSON array, no markdown:
[{\"title\":\"...\",\"action\":\"...\",\"category\":\"investor|customer|product|team|legal|admin\",\"priority\":\"high|medium|low\",\"deadline\":\"today|this-week|no-deadline\"}]",

'briefing' => "Generate a sharp daily briefing with exactly these sections:
**🔥 Top 3 Priorities Today**
**⚠️ At Risk Of Missing**
**📊 One Metric To Watch**
**💡 Founder Insight**
Be direct. No fluff.",

'pitch' => "You are FounderBrain's Pitch Coach. Ask the toughest investor questions. Draft model answers. Identify narrative weaknesses. Be the hardest, most helpful pitch coach they've ever had.",
];

$system   = $systems[$mode] ?? $systems['chat'];
$contents = [];
foreach ($messages as $msg) {
    $contents[] = ['role'=>$msg['role']==='assistant'?'model':'user','parts'=>[['text'=>$msg['content']]]];
}

$payload = [
    'system_instruction' => ['parts'=>[['text'=>$system]]],
    'contents'           => $contents,
    'generationConfig'   => ['temperature'=>0.7,'maxOutputTokens'=>1200],
];

$url = 'https://generativelanguage.googleapis.com/v1beta/models/'.GEMINI_MODEL.':generateContent?key='.GEMINI_API_KEY;

$response = null; $code = 0;
for ($i=1; $i<=3; $i++) {
    $ch = curl_init($url);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
        CURLOPT_POSTFIELDS=>json_encode($payload),
        CURLOPT_HTTPHEADER=>['Content-Type: application/json'],CURLOPT_TIMEOUT=>30]);
    $response = curl_exec($ch);
    $code     = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code===200) break;
    if ($i<3) sleep(2);
}

if ($code!==200) { echo json_encode(['reply'=>'⚠️ AI error (HTTP '.$code.'). Try again in a moment.']); exit; }

$data = json_decode($response,true);
$text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response.';

// Log to DB
try {
    if (!empty($messages)) {
        $last = end($messages);
        $st = db()->prepare('INSERT INTO chat_messages(user_id,session_id,role,content,mode)VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE content=content');
        db()->prepare('CREATE TABLE IF NOT EXISTS chat_messages(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,session_id VARCHAR(100),role VARCHAR(20),content TEXT,mode VARCHAR(50),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)')->execute();
        $st->execute([$_SESSION['user_id'],session_id(),'user',$last['content'],$mode]);
        $st->execute([$_SESSION['user_id'],session_id(),'assistant',$text,$mode]);
    }
} catch(Exception $e){}

echo json_encode(['reply'=>$text]);
