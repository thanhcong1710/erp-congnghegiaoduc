import { GoogleGenerativeAI, SchemaType } from "@google/generative-ai";
import dotenv from "dotenv";

// Load biến môi trường
dotenv.config();
const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// ==========================================
// 1. ĐỊNH NGHĨA TOOL (CÔNG CỤ)
// ==========================================
// Tool là các hàm/chức năng (Function Calling) giúp LLM giao tiếp với hệ thống bên ngoài.
function lookupStudentDB(studentId) {
    const db = {
        "SV001": { name: "Nguyễn Văn A", class: "10A1", tuition_status: "Chưa đóng", debt_amount: "5,000,000 VND" },
        "SV002": { name: "Trần Thị B", class: "11B2", tuition_status: "Đã đóng", debt_amount: "0 VND" },
    };
    return db[studentId] || { error: "Không tìm thấy học sinh" };
}

const dbLookupTool = {
    functionDeclarations: [{
        name: "lookupStudentDB",
        description: "Truy xuất thông tin học phí của học sinh từ cơ sở dữ liệu.",
        parameters: {
            type: SchemaType.OBJECT,
            properties: { 
                studentId: { type: SchemaType.STRING, description: "Mã học sinh (VD: SV001)" } 
            },
            required: ["studentId"],
        },
    }],
};

// ==========================================
// 2. ĐỊNH NGHĨA SKILL (KỸ NĂNG)
// ==========================================
// Skill là một năng lực cụ thể của Agent. Ở đây ta dùng System Instruction 
// để trang bị Kỹ năng "Tư vấn và Chăm sóc khách hàng" chuyên nghiệp.
const advisingSkillInstruction = "Bạn là một chuyên gia tư vấn học vụ xuất sắc của trường học. Kỹ năng của bạn là: giao tiếp lịch sự, khéo léo thông báo học phí, và luôn đề xuất giải pháp hỗ trợ phụ huynh. Luôn xưng 'em' và gọi khách hàng là 'Quý Phụ Huynh'.";

// ==========================================
// KHỞI TẠO CÁC MODEL CHO 4 TRƯỜNG HỢP
// ==========================================

// 1. Thuần LLM
const modelPureLLM = genAI.getGenerativeModel({ model: "gemini-3.5-flash" });

// 2. Chỉ dùng Tool (Có khả năng lấy dữ liệu, nhưng trả lời máy móc)
const modelWithTool = genAI.getGenerativeModel({ 
    model: "gemini-3.5-flash",
    tools: [dbLookupTool]
});

// 3. Chỉ có Skill (Ăn nói khéo léo, nhưng không có công cụ lấy dữ liệu)
const modelWithSkill = genAI.getGenerativeModel({ 
    model: "gemini-3.5-flash",
    systemInstruction: advisingSkillInstruction
});

// 4. Có Skill kết hợp Tool (Agent Hoàn Chỉnh)
const modelAgent = genAI.getGenerativeModel({ 
    model: "gemini-3.5-flash",
    systemInstruction: advisingSkillInstruction,
    tools: [dbLookupTool]
});

// ==========================================
// HÀM HỖ TRỢ XỬ LÝ TOOL CALL
// ==========================================
async function handleChatWithTool(chat, prompt) {
    const result = await chat.sendMessage(prompt);
    const call = result.response.functionCalls();

    if (call && call.length > 0) {
        const functionCall = call[0];
        console.log(`   [⚙️ Tool Call] LLM yêu cầu gọi hàm: ${functionCall.name}(${JSON.stringify(functionCall.args)})`);
        
        // Chạy hàm thực tế
        const data = lookupStudentDB(functionCall.args.studentId);
        console.log(`   [📦 Data] Kết quả từ Database:`, data);

        // Gửi lại dữ liệu cho LLM
        const finalResult = await chat.sendMessage([{
            functionResponse: { name: functionCall.name, response: data }
        }]);
        return finalResult.response.text().trim();
    } else {
        return result.response.text().trim();
    }
}

// ==========================================
// CHẠY THỬ NGHIỆM
// ==========================================
async function runComparison() {
    const prompt = "Hãy kiểm tra xem học sinh SV001 đã đóng học phí chưa và phản hồi lại cho phụ huynh giúp tôi.";
    
    console.log("===============================================================");
    console.log(`👤 YÊU CẦU CỦA BẠN: "${prompt}"`);
    console.log("===============================================================\n");

    // --- CASE 1: THUẦN LLM ---
    console.log("🔴 1. THUẦN LLM (Pure LLM)");
    console.log("   -> Không có Tool (không biết SV001 là ai), Không có Skill tư vấn.");
    try {
        const result1 = await modelPureLLM.generateContent(prompt);
        console.log(`\n🤖 TRẢ LỜI: "${result1.response.text().trim()}"\n`);
    } catch (e) { console.log("Lỗi:", e.message); }

    // --- CASE 2: CHỈ CÓ TOOL ---
    console.log("---------------------------------------------------------------");
    console.log("🟡 2. CHỈ CÓ TOOL (Using Tool)");
    console.log("   -> Biết cách gọi DB lấy dữ liệu thật, nhưng trả lời theo bản năng gốc.");
    try {
        const chat2 = modelWithTool.startChat();
        const response2 = await handleChatWithTool(chat2, prompt);
        console.log(`\n🤖 TRẢ LỜI: "${response2}"\n`);
    } catch (e) { console.log("Lỗi:", e.message); }

    // --- CASE 3: CHỈ CÓ SKILL ---
    console.log("---------------------------------------------------------------");
    console.log("🔵 3. CHỈ CÓ SKILL (Using Skill)");
    console.log("   -> Có kỹ năng giao tiếp cực kỳ khéo léo, nhưng không có công cụ lấy data thực (buộc phải bịa hoặc từ chối).");
    try {
        const result3 = await modelWithSkill.generateContent(prompt);
        console.log(`\n🤖 TRẢ LỜI: "${result3.response.text().trim()}"\n`);
    } catch (e) { console.log("Lỗi:", e.message); }

    // --- CASE 4: SKILL + TOOL ---
    console.log("---------------------------------------------------------------");
    console.log("🟢 4. SKILL + TOOL (Agent Hoàn chỉnh)");
    console.log("   -> Vừa lấy được data chính xác (Tool), vừa phản hồi siêu khéo léo (Skill).");
    try {
        const chat4 = modelAgent.startChat();
        const response4 = await handleChatWithTool(chat4, prompt);
        console.log(`\n🤖 TRẢ LỜI: "${response4}"\n`);
    } catch (e) { console.log("Lỗi:", e.message); }

    console.log("===============================================================\n");
}

runComparison();

