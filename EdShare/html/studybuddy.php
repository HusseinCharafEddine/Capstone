<?php
if ($_POST) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $post_data = array(
        "model" => "babbage-002",
        "prompt" => $_POST['question'],
        "temperature" => 0.4,
        "max_tokens" => 64,
        "top_p" => 1,
        "frequency_penalty" => 0,
        "presence_penalty" => 0
    );

    $post_data = json_encode($post_data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Bearer sk-proj-TaCeg2L426vT8Ph83vnUT3BlbkFJBp6dgjaSiRzU4wDqyUtw'; // Replace 'YOUR_API_KEY' with your actual OpenAI API key
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);
}
?>

<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<div class="container">
    <div class="row form-group">
        <div class="col-xs-12 col-md-offset-2 col-md-8 col-lg-8 col-lg-offset-2">
            <div class="panel panel-primary">
                <div class="panel-body body-panel">
                    <ul class="chat">
                        <?php
                        if (isset($_POST['question']) && !empty($_POST['question'])) {
                            ?>
                            <li class="right clearfix"><span class="chat-img pull-right">
                                <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" /> </span>
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <small class="text-muted"><span class="glyphicon glyphicon-time"></span><?php echo date('Hi'); ?></small> 
                                        <strong class="pull-right primary-font">You</strong>
                                    </div>
                                    <p>
                                        <?php echo $_POST['question']; ?>
                                    </p>
                                </div>
                            </li>
                            <?php } ?>
                            <?php
                            if (isset($result) && !empty($result)) {
                                ?>
                                <li class="left clearfix">
                                    <span class="chat-img pull-left">
                                        <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                                    </span>
                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">Answer</strong>
                                            <small class="pull-right text-muted">
                                                <span class="glyphicon glyphicon-time"></span>
                                                <?php echo date('Hi'); ?>
                                            </small>
                                        </div>
                                        <p>
                                            <?php 
                                            $resultArray = json_decode($result, true);

                                            if (isset($resultArray['error'])) {
                                                if (is_array($resultArray['error'])) {
                                                    echo "API Error: " . implode(", ", $resultArray['error']);
                                                } else {
                                                    echo "API Error: " . $resultArray['error'];
                                                }
                                            } else {
                                                var_dump($resultArray); // or print_r($resultArray);
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </li>
                            <?php } ?>

                        </ul>
                    </div>
                    <form method="post" name="chatform">
                        <div class="panel-footer clearfix">
                            <textarea class="form-control" rows="3" name="question" id="question"></textarea>
                            <span class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-xs-12" style="margin-top: 10px">
                                <button type="submit" class="btn btn-warning btn-lg btn-block" id="btn-chat" name="submit">Submit </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
